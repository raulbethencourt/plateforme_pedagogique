<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Form\QuestionType;
use App\Repository\QuestionnaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
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
     * @Route("/teacher", name="teacher_index")
     * @param QuestionnaireRepository $repository
     * @return ResponseAlias
     */
    public function index(QuestionnaireRepository $repository): ResponseAlias
    {
        $teacher = $this->getUser();
        return $this->render(
            'teacher/index.html.twig',
            [
                'questionnaires' => $teacher->getQuestionnaires(),
                'teacher' => $teacher,
            ]
        );
    }

    /**
     * @Route("/teacher/questionnaire/create", name="questionnaire_create")
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function createQuestionnaire(Questionnaire $questionnaire = null, Request $request)
    {
        if (!$questionnaire) {
            $questionnaire = new Questionnaire();
        }

        $questionnaire->setDateCreation(new \DateTime());
        $questionnaire->setTeacher($this->getUser());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'questionnaire ajouté avec succès');

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
            ]
        );
    }

    /**
     * @Route ("/teacher/questionnaire/{id}", name="questionnaire_edit", methods={"GET","POST"})
     */
    public function editQuestionnaire(Questionnaire $questionnaire, Request $request)
    {
        $questionnaire->setTeacher($this->getUser());
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'questionnaire modifié avec succès');

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
            ]
        );
    }

    /**
     * @Route ("/teacher/questionnaire/{id}", name="questionnaire_delete", methods={"DELETE"})
     * @param Questionnaire $questionnaire
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteQuestionnaire(Questionnaire $questionnaire, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$questionnaire->getId(), $request->get('_token'))) {
            $this->em->remove($questionnaire);
            $this->em->flush();
            $this->addFlash('succes', 'questionnaire supprimé avec succès');
        }

        return $this->redirectToRoute('teacher_index');
    }


    /**
     * @Route("/teacher/question_create/{id}", name="question_create", methods={"GET","POST"})
     * @param Questionnaire $questionnaire
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function createQuestion(Questionnaire $questionnaire, Request $request)
    {
        $question = new Question();

        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);
        $questionnaire_id = $questionnaire->getId();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'question ajouté avec succès');

            return $this->redirectToRoute(
                'question_create',
                [
                    'id' => $questionnaire_id,
                ]
            );
        }

        return $this->render(
            'question/new.html.twig',
            [
                'question' => $question,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/teacher/question_edit/{id}", name="question_edit", methods={"GET","POST"})
     * @param Question|null $question
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function editQuestion(Question $question, Request $request)
    {
        $questionnaire = $request->attributes->get('question');
        $questionnaire = (array)$questionnaire;
        $questionnaire = $questionnaire["\x00App\Entity\Question\x00questionnaire"];
        $questionnaire_id = (array)$questionnaire;
        $questionnaire_id = $questionnaire_id["\x00App\Entity\Questionnaire\x00id"];

        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'question ajouté avec succès');

            return $this->redirectToRoute(
                'questionnaire_index',
                [
                    'id' => $questionnaire_id,
                ]
            );
        }

        return $this->render(
            'question/edit.html.twig',
            [
                'question' => $question,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/teacher/question_delete/{id}", name="question_delete", methods={"DELETE"})
     * @param Questionnaire $questionnaire
     * @param Question $question
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteQuestion(Questionnaire $questionnaire, Question $question, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->get('_token'))) {
            $this->em->remove($question);
            $this->em->flush();
            $this->addFlash('succes', 'questionnaire supprimé avec succès');
        }

        return $this->redirectToRoute(
            'questionnaire_index',
            [
                'id' => $questionnaire,
            ]
        );
    }
}
