<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LessonController
 * This class manage the lessons.
 *
 * @Route("/lesson")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
 */
class LessonController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
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
     * @Route("/{id}", name="lesson_index", requirements={"id": "\d+"})
     */
    public function index(): Response
    {
        if (null !== $this->request->query->get('classroom_id')) {
            $classroom = $this->find->findClassroom();
        } else {
            $classroom = null;
        }
        $lesson = $this->find->findLesson();

        return $this->render('lesson/index.html.twig', [
            'lesson' => $lesson,
            'questionnaires' => $lesson->getQuestionnaires(),
            'classroom' => $classroom,
        ]);
    }

    /**
     * @Route("/list", name="list_lessons")
     */
    public function listLessons(): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');
        $lessons = $this->find->findAllLessons();

        return $this->render('lesson/list.html.twig', [
            'lessons' => $lessons,
            'classroom_id' => $classroom_id,
        ]);
    }

    /**
     * @Route("/create", name="lesson_create")
     */
    public function createLesson(): Response
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
                return $this->redirectToRoute(
                    'lesson_index',
                    [
                        'id' => $lesson->getId(),
                        'classroom' => $classroom->getId(),
                    ]
                );
            }

            return $this->redirectToRoute('list_lessons');
        }

        return $this->render(
            'lesson/new.html.twig',
            [
                'classroom' => $classroom,
                'lesson' => $lesson,
                'form' => $form->createView(),
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="lesson_edit", methods={"GET", "POST"})
     */
    public function editLesson(): Response
    {
        $lesson = $this->find->findLesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Module modifiée avec succès.');

            return $this->redirectToRoute('list_lessons');
        }

        return $this->render(
            'lesson/edit.html.twig',
            [
                'lesson' => $lesson,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", name="lesson_delete", methods={"DELETE"})
     */
    public function deleteLesson(): RedirectResponse
    {
        $lesson = $this->find->findLesson();

        if ($this->isCsrfTokenValid(
            'delete'.$lesson->getId(),
            $this->request->get('_token')
        )) {
            $this->em->remove($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimée avec succès.');
        }

        return $this->redirectToRoute('list_lessons');
    }

    /**
     * Add questionnaire direct to a lesson.
     *
     * @Route("/questionnaire/add", name="add_questionnaire_lesson")
     */
    public function addQuestionnaireToLesson(): RedirectResponse
    {
        $lesson = $this->find->findLesson();
        $questionnaire = $this->find->findQuestionnaire();
        $classroom_id = $this->request->query->get('classroom');

        $lesson->addQuestionnaire($questionnaire);
        $this->em->persist($lesson);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté avec succès.');

        return $this->redirectToRoute(
            'lesson_index',
            [
                'id' => $lesson->getId(),
                'classroom' => $classroom_id,
            ]
        );
    }

    /**
     * Delete a questionnaire from a lesson and not in database.
     *
     * @Route("/questionnaire/{id}/delete", name="delete_questionnaire_lesson", methods={"DELETE"})
     */
    public function deleteQuestionnaireFromLesson(): RedirectResponse
    {
        $lesson = $this->find->findLesson();
        $questionnaire = $this->find->findQuestionnaire();
        $classroom_id = $this->request->query->get('classroom');

        
        if ($this->isCsrfTokenValid(
            'delete'.$lesson->getId(),
            $this->request->get('_token')
            )) {
            $lesson->removeQuestionnaire($questionnaire);
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Activité supprimé avec succès.');
        }

        return $this->redirectToRoute('lesson_index', [
            'id' => $lesson->getId(),
            'classroom_id' => $classroom_id,
        ]);
    }
}
