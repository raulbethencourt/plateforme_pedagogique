<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * This class manage the questionnaires plays
 * @Route ("/questionnaire")
 * @package App\Controller
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @Route ("/{id}", name="questionnaire_index")
     * @IsGranted ("ROLE_TEACHER")
     * @param  Questionnaire  $questionnaire
     * @param  Request  $request
     * @return Response
     */
    public function index(Questionnaire $questionnaire, Request $request): Response
    {
        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questionnaire' => $questionnaire,
                'teacher' => $this->getUser(),
            ]
        );
    }

    /**
     * This methode control the questionnaires gaming
     * @Route("/{id}/play", name="questionnaire_play")
     * @IsGranted("ROLE_STUDENT")
     * @param  Questionnaire  $questionnaire
     * @param  Request  $request
     * @return Response
     */
    public function play(Questionnaire $questionnaire, Request $request): Response
    {
        // Check if we can play the questionnaire or not
        if (!$questionnaire->isPlayable()) {
            $this->addFlash('error', 'Questionnaire indisponible !');

            return $this->redirectToRoute('student_index');
        }

        // Creates the variables that I'm gonna need later on
        $answers = null;
        $rights = null;
        $points = null;

        if ($request->isMethod("post")) {
            $answers = $request->request; //equivalent à $_POST

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
            $pass->setDateRealisation(new \DateTime());
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
                'student' => $this->getUser(),
            ]
        );
    }

    /**
     * This methode checks questionnaire answers
     * @param $answers
     * @param $questionnaire
     * @return array
     */
    private function evaluateQuestionnaire($answers, $questionnaire): array
    {
        $points = 0;
        $correctPropositions = [];

        // For each questionnaire question we check if the student has chosen a good answer
        foreach ($questionnaire->getQuestions() as $question) {
            // Call a methode in question Entity
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
