<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * @package App\Controller
 * @Route ("/questionnaire")
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @Route ("/{id}", name="questionnaire_index")
     * @param Questionnaire $questionnaire
     * @param Request $request
     * @return Response
     */
    public function index(Questionnaire $questionnaire, Request $request): Response
    {
        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questionnaire' => $questionnaire,
            ]
        );
    }

    /**
     * @Route("/play/{id}", name="questionnaire_play")
     * @param Questionnaire $questionnaire
     * @param Request $request
     * @return Response
     */
    public function play(Questionnaire $questionnaire, Request $request): Response
    {
        if (!$questionnaire->isPlayable()) {
            $this->addFlash('error', 'Questionnaire indisponible !');

            return $this->redirectToRoute('student_index');
        }

        $answers = null;
        $rights = null;
        $points = null;

        if ($request->isMethod("post")) {
            $answers = $request->request;//equivalent à $_POST

            $eval = $this->evaluateQuestionnaire($answers, $questionnaire);
            $rights = $eval['corrects'];
            $points = $eval['points'];

            $em = $this->getDoctrine()->getManager();
            $pass = $em->getRepository(Pass::class)->findOneBy(
                ['student' => $this->getUser(), "questionnaire" => $questionnaire]
            );

            if (!$pass) {
                $pass = new Pass();
                $pass->setStudent($this->getUser());
                $pass->setQuestionnaire($questionnaire);
            }

            $pass->setPoints($points);
            $pass->setDateRealisation(new\ DateTime());
            $em->persist($pass);
            $em->flush();
        }

        return $this->render(
            'questionnaire/play.html.twig',
            [
                "questionnaire" => $questionnaire,
                "questions" => $questionnaire->getQuestions(),
                "points" => $points,
                "finalResults" => [
                    "given" => $answers,
                    "rights" => $rights,
                ],
            ]
        );
    }

    /**
     * @param $answers
     * @param $questionnaire
     * @return array
     */
    private function evaluateQuestionnaire($answers, $questionnaire): array
    {
        $points = 0;
        $correctPropositions = [];

        foreach ($questionnaire->getQuestions() as $question) {
            $rightPropositions = $question->getRightPropositions();

            foreach ($rightPropositions as $rightProposition) {
                $rightProposition = $rightProposition->getId();
                if ($answers->get($question->getId()) == $rightProposition) {
                    $correctPropositions[] = $rightProposition;
                    $points += $question->getScore();
                }
            }
        }

        return ["corrects" => $correctPropositions, "points" => $points];
    }
}

