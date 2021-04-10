<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * This class manage the questionnaires plays and creation.
 *
 * @Route("/questionnaire")
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $find;

    private $request;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $request)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @Route("/{id}", name="questionnaire_index", requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function index(): Response
    {
        $questionnaire = $this->find->findQuestionnaire();

        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questionnaire' => $questionnaire,
                'questions' => $questionnaire->getQuestions(),
                'lesson_id' => $this->request->query->get('lesson_id')
            ]
        );
    }

    /**
     * @Route("/list", name="list_questionnaires")
     */
    public function listQuestionnaires(): Response
    {
        return $this->render('questionnaire/list.html.twig', [
            'questionnaires' => $this->find->findAllQuestionnaires(),
            'lesson_id' => $this->request->query->get('lesson_id'),
            'classroom_id' => $this->request->query->get('classroom_id'),
        ]);
    }

    /**
     * @Route("/create", name="questionnaire_create")
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function createQuestionnaire(): Response
    {
        $lesson = $this->find->findLesson();
        $questionnaire = new Questionnaire();
        $questionnaire->addLesson($lesson);
        $questionnaire->setDateCreation(new \DateTime());
        $questionnaire->setCreator($this->getUser()->getUsername());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Questionnaire ajouté avec succès.');

            return $this->redirectToRoute(
                'question_create',
                [
                    'id' => $questionnaire->getId(),
                    'lesson_id' => $lesson->getId(),
                ]
            );
        }

        return $this->render(
            'questionnaire/new.html.twig',
            [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'lesson_id' => $lesson->getId(),
                'classroom_id' => $this->request->query->get('classroom'),
            ]
        );
    }

    /**
     * @Route("/{id}", name="questionnaire_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function editQuestionnaire(): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'Questionnaire modifié avec succès.');

            return $this->redirectToRoute(
                'lesson_index',
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
     * @Route("/questionnaire/{id}", name="questionnaire_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function deleteQuestionnaire(): RedirectResponse
    {
        $questionnaire = $this->find->findQuestionnaire();
        // check the token to delete
        if ($this->isCsrfTokenValid('delete'.$questionnaire->getId(), $this->request->get('_token'))) {
            $this->em->remove($questionnaire);
            $this->em->flush();
            $this->addFlash('success', 'Questionnaire supprimé avec succès.');
        }

        return $this->redirectToRoute('lesson_index');
    }

    /**
     * This methode control the questionnaires gaming.
     *
     * @Route("/{id}/play", name="questionnaire_play")
     * @Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function play(): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        // Check if we can play the questionnaire or not
        if (!$questionnaire->isPlayable()) {
            $this->addFlash('error', 'Questionnaire indisponible !');

            return $this->redirectToRoute('student_index');
        }

        // Creates the variables that I'm gonna need later on
        $answers = null;
        $rights = null;
        $points = null;

        if ($this->request->isMethod('post')) {
            $answers = $this->request->request; //equivalent à $_POST
            $eval = $this->evaluateQuestionnaire($answers, $questionnaire);
            $rights = $eval['corrects'];
            $points = $eval['points'];

            if ('ROLE_STUDENT' === $this->getUser()->getRoles()[0]) {
                $pass = $this->find->findPass($this->getUser(), $questionnaire);

                if (!$pass) {
                    $pass = new Pass();
                    $pass->setStudent($this->getUser());
                    $pass->setQuestionnaire($questionnaire);
                }

                $pass->setPoints($points);
                $pass->setDateRealisation(new \DateTime());
                $this->em->persist($pass);
                $this->em->flush();
            }
        }

        return $this->render(
            'questionnaire/play.html.twig',
            [
                'questionnaire' => $questionnaire,
                'questions' => $questionnaire->getQuestions(),
                'points' => $points,
                'finalResults' => [
                    'given' => $answers,
                    'rights' => $rights,
                ],
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * This methode checks questionnaire answers.
     */
    private function evaluateQuestionnaire(ParameterBag $answers, Questionnaire $questionnaire): array
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

        return ['corrects' => $correctPropositions, 'points' => $points];
    }
}
