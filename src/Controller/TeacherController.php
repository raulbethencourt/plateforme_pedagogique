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

/**
 * @Route("/teacher", name="teacher")
 */
class TeacherController extends AbstractController
{
    /**
     * @var QuestionnaireRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(QuestionnaireRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/{id}", name="teacher_index")
     */
    public function index()
    {
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }

    /**
     * @Route("/questionnaire/create", name="questionnaire_create")
     * @param Questionnaire|null $questionnaire
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function create(Questionnaire $questionnaire = null, Request $request)
    {
        if (!$questionnaire) {
            $questionnaire = new Questionnaire();
        }

        $form = $this->createForm(QuestionnaireType::class, $questionnaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaire = $form->getData();

            $this->em->persist($questionnaire);
            $this->em->flush();

            $this->addFlash('success', 'questionnaire ajouté avec succès');

            return $this->redirectToRoute('question_create');
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
     * @Route("/question/create", name="question_create")
     * @param  Question|null  $question
     * @param  Request  $request
     * @return RedirectResponse|ResponseAlias
     */
    public function newQuestion(Question $question = null,  Request $request)
    {
        if (!$question) {
            $question = new Question();
        }

        $questionnaire = new Questionnaire();

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'question ajouté avec succès');

            return $this->redirectToRoute('questionnaire/index.html.twig');
        }

        return $this->render(
            'question/new.html.twig',
            [
                'questionnaire' => $questionnaire,
                'question' => $question,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/questionnaire/edit/{id}", name="questionnaire_edit", methods={"GET","POST"})
     */
    public function editQuestionnaire(Questionnaire $questionnaire, Request $request)
    {
        $form = $this->createForm(Questionnaire::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'questionnaire modifié avec succes');

            return $this->redirectToRoute('teacher_index');
        }

        return $this->render(
            'teacher/edit.html.twig',
            [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/questionnaire/delete/{id}", name="questionnaire_delete")
     * @param  Questionnaire  $questionnaire
     * @param  Request  $request
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
}
