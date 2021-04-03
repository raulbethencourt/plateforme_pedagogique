<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuestionnaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class QuestionnaireController
 * This class manage the questionnaires plays and creation
 * @Route ("/questionnaire")
 * @package App\Controller
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route ("/{id}", name="questionnaire_index", requirements={"id":"\d+"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     * @param \App\Entity\Questionnaire $questionnaire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Questionnaire $questionnaire): Response
    {
        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questionnaire' => $questionnaire,
                'questions' => $questionnaire->getQuestions()
            ]
        );
    }

    /**
     * @Route("/list", name="list_questionnaires")
     * @param \App\Repository\QuestionnaireRepository $repository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listQuestionnaires(QuestionnaireRepository $repository, Request $request): Response
    {
        return $this->render('questionnaire/list.html.twig', [
            'questionnaires' => $repository->findAll(),
            'lesson_id' =>  $request->query->get('lesson_id'),
            'classroom' => $request->query->get('classroom_id') 
        ]);
    }

    /**
     * @Route("/create", name="questionnaire_create")
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     * @ParamConverter("questionnaire", class="\App\Entity\Questionnaire")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createQuestionnaire(Request $request, LessonRepository $lessonRepo): Response
    {
        $questionnaire = new Questionnaire();

        // Add actual date/time and the Lesson in the creation
        // Get the lesson
        $lesson_id = $request->query->get('lesson');
        $lesson = $lessonRepo->findOneById($lesson_id);
        $questionnaire->addLesson($lesson);
        $questionnaire->setDateCreation(new \DateTime());
        $questionnaire->setCreator($this->getUser()->getUsername());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Questionnaire ajouté avec succès.');

            return $this->redirectToRoute(
                'question_create',
                [
                    'id' => $questionnaire->getId(),
                ]
            );
        }

        return $this->render(
            'questionnaire/new.html.twig',
            [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'lesson_id' => $lesson_id,
                'classroom_id' => $request->query->get('classroom')
            ]
        );
    }

    /**
     * @Route ("/{id}", name="questionnaire_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     * @param \App\Entity\Questionnaire $questionnaire
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editQuestionnaire(Questionnaire $questionnaire, Request $request): Response
    {
        $questionnaire->addLesson($this->getUser());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Questionnaire modifié avec succès.');

            return $this->redirectToRoute(
                'teacher_index',
                [
                    'id' => $questionnaire->getId(),
                ]
            );
        }

        return $this->render(
            'questionnaire/edit.html.twig',
            [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route ("/questionnaire/{id}", name="questionnaire_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     * @param  Questionnaire  $questionnaire
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function deleteQuestionnaire(Questionnaire $questionnaire, Request $request): RedirectResponse
    {
        // check the token to delete
        if ($this->isCsrfTokenValid('delete' . $questionnaire->getId(), $request->get('_token'))) {
            $this->em->remove($questionnaire);
            $this->em->flush();
            $this->addFlash('success', 'Questionnaire supprimé avec succès.');
        }

        return $this->redirectToRoute('lesson_index');
    }


    /**
     * This methode control the questionnaires gaming
     * @Route("/{id}/play", name="questionnaire_play")
     * @Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
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
            // TODO mirar si es un estudiante o un profesor que juega 
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
                'user' => $this->getUser(),
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
