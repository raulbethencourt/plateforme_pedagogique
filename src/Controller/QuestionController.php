<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Form\QuestionType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs as ModelBreadcrumbs;

/**
 * @Route("/question")
 * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
 */
class QuestionController extends AbstractController
{
    private $em;
    private $find;
    private $request;
    private $breadCrumbs;


    public function __construct(
        EntityManagerInterface $em, 
        FindEntity $find, 
        RequestStack $requestStack, 
        BreadCrumbs $breadCrumbs
    ) {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/new", name="question_new", methods={"GET", "POST"})
     */
    public function new(): Response
    {
        $this->questionBC(null, 'new');
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
                    'classroom_id' => $this->request->get('classroom_id'),
                    'list' => $this->request->get('list'),
                    'lonely' => $this->request->get('lonely'),
                    'extra' => $this->request->get('extra'),
                ]
            );
        }

        return $this->render('question/new.html.twig', [
            'questionnaire' => $questionnaire,
            'question' => $question,
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'lesson_id' => $this->request->query->get('lesson_id'),
            'questionnaire_id' => $this->request->get('questionnaire_id'),
            'classroom_id' => $this->request->get('classroom_id'),
            'list' => $this->request->get('list'),
            'lonely' => $this->request->get('lonely'),
            'extra' => $this->request->get('extra'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET", "POST"})
     */
    public function edit(Question $question): Response
    {
        $this->questionBC($question, 'edit');
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
                'lesson_id' => $this->request->get('lesson_id'),
                'classroom_id' => $this->request->get('classroom_id'),
                'list' => $this->request->get('list'),
                'lonely' => $this->request->get('lonely'),
                'extra' => $this->request->get('extra'),
            ]);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'questionnaire_id' => $this->request->get('questionnaire_id'),
            'lesson_id' => $this->request->get('lesson_id'),
            'classroom_id' => $this->request->get('classroom_id'),
            'list' => $this->request->get('list'),
            'lonely' => $this->request->get('lonely'),
            'extra' => $this->request->get('extra'),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods={"DELETE"})
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
            'lesson_id' => $this->request->get('lesson_id'),
            'classroom_id' => $this->request->get('classroom_id'),
            'list' => $this->request->get('list'),
            'lonely' => $this->request->get('lonely'),
            'extra' => $this->request->get('extra'),
        ]);
    }

    /**
     * Helping methods to call breadcrumbsService.
     */
    private function questionBC(?Question $question, string $method): ModelBreadcrumbs
    {
        return $this->breadCrumbs->bcQuestion(
            $question,
            $method,
            $this->request->get('classroom_id'),
            $this->request->get('lesson_id'),
            $this->request->get('questionnaire_id'),
            $this->request->get('list'),
            $this->request->get('lonely'),
            $this->request->get('extra'),   
        );
    }
}
