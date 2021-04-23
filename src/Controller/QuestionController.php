<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Service\FindEntity;
use App\Entity\Questionnaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/question")
 * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
 */
class QuestionController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Route("/new", name="question_new", methods={"GET", "POST"})
     */
    public function new(): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        $questionnaire_id = $questionnaire->getId();
        $lesson_id = $this->request->query->get('lesson_id');
        $question = new Question();

        // Link question to his questionnaire
        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();
            $this->addFlash('success', 'Question ajoutée avec succès.');

            return $this->redirectToRoute(
                'question_new',
                [
                    'questionnaire_id' => $questionnaire_id,
                    'lesson_id' => $lesson_id,
                ]
            );
        }

        return $this->render('question/new.html.twig', [
            'questionnaire' => $questionnaire,
            'question' => $question,
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'lesson_id' => $this->request->query->get('lesson_id'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET", "POST"})
     */
    public function edit(Question $question): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'Question editée avec succès.');

            return $this->redirectToRoute('questionnaire_show', [
                'id' => $this->request->query->get('questionnaire_id'),
            ]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods={"POST"})
     */
    public function delete(Question $question): RedirectResponse
    {
        // Check the token for validation
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $this->request->get('_token'))) {
            $this->em->remove($question);
            $this->em->flush();
            $this->addFlash('succes', 'Questionnaire supprimé avec succès.');
        }

        return $this->redirectToRoute('questionnaire_show', [
            'id' => $this->request->query->get('questionnaire_id'),
        ]);
    }
}
