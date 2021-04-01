<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Entity\Questionnaire;
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
 * Class QuestionController
 * This class manage the questions
 * @Route ("/question")
 * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
 * @package App\Controller
 */
class QuestionController extends AbstractController
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
     * @Route("/create/{id}", name="question_create", methods={"GET","POST"})
     * @ParamConverter("question", class="\App\Entity\Question")
     * @param \App\Entity\Questionnaire $questionnaire
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createQuestion(Questionnaire $questionnaire, Request $request): Response
    {
        $question = new Question();
        $questionnaire_id = $questionnaire->getId();

        // Link question to his questionnaire
        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'Question ajoutée avec succès.');

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
                'questionnaire' => $questionnaire,
                'question' => $question,
                'form' => $form->createView(),
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="question_edit", methods={"GET","POST"})
     * @param \App\Entity\Question|null $question
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editQuestion(Question $question, Request $request, QuestionnaireRepository $repository): Response
    {
        $questionnaire_id = $request->query->get('questionnaire');
        $questionnaire = $repository->findOneById($questionnaire_id);

        $question->setQuestionnaire($questionnaire);
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', 'Question ajoutée avec succès.');

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
     * @Route ("/delete/{id}", name="question_delete", methods={"DELETE"})
     * @param \App\Entity\Question $question
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteQuestion(Question $question, Request $request): RedirectResponse
    {
        // Check the token for validation
        if ($this->isCsrfTokenValid(
            'delete' . $question->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($question);
            $this->em->flush();
            $this->addFlash('succes', 'Questionnaire supprimé avec succès.');
        }

        return $this->redirectToRoute(
            'questionnaire_index',
            [
                'id' => $request->query->get('questionnaire'),
            ]
        );
    }
}
