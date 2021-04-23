<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Service\FindEntity;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
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
     * @Route("/", name="lesson_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function index(LessonRepository $lessonRepo): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepo->findAll(),
            'classroom_id' => $this->request->query->get('classroom_id'),
        ]);
    }

    /**
     * @Route("/new", name="lesson_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function new(): Response
    {
        $classroom = $this->find->findClassroom();
        $lesson = new Lesson();
        if (isset($classroom)) {
            $lesson->addClassroom($classroom);
        }
        $lesson->setDateCreation(new \DateTime());
        $lesson->addUser($this->getUser());
        $lesson->setCreator($this->getUser()->getUsername());
        $form = $this->createForm(LessonType::class, $lesson);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module ajouté avec succès.');

            if (isset($classroom)) {
                return $this->redirectToRoute('lesson_show', [
                    'id' => $lesson->getId(),
                    'classroom' => $classroom->getId(),
                ]);
            }

            return $this->redirectToRoute('lesson_show', [
                'id' => $lesson->getId(),
            ]);
        }

        return $this->render('lesson/new.html.twig', [
            'classroom' => $classroom,
            'lesson' => $lesson,
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or is_granted('ROLE_STUDENT')")
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'questionnaires' => $lesson->getQuestionnaires(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function edit(Lesson $lesson): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Module modifiée avec succès.');

            return $this->redirectToRoute('lesson_index');
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="lesson_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function deleteLesson(Lesson $lesson): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $this->request->get('_token'))) {
            $this->em->remove($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimée avec succès.');
        }

        return $this->redirectToRoute('lesson_index');
    }

    /**
     * @Route("/add_questionnaire", name="lesson_questionnaire_add", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function addQuestionnaireToLesson(Lesson $lesson): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        $lesson->addQuestionnaire($questionnaire);
        $this->em->persist($lesson);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté avec succès.');

        return $this->redirectToRoute('lesson_show', [
            'id' => $lesson->getId(),
            'classroom' => $this->request->query->get('classroom'),
        ]);
    }

    /**
     * @Route("/{id}/questionnaire_remove", name="lesson_questionnaire_remove", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function removeQuestionnaireFromLesson(Lesson $lesson): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $this->request->get('_token'))) {
            $lesson->removeQuestionnaire($questionnaire);
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Activité supprimé avec succès.');
        }

        return $this->redirectToRoute('lesson_show', [
            'id' => $lesson->getId(),
            'classroom_id' => $this->request->query->get('classroom'),
        ]);
    }
}
