<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Service\FindEntity;
use App\Repository\LessonRepository;
use App\Repository\ClassroomRepository;
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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/{id}", name="lesson_index", requirements={"id": "\d+"})
     */
    public function index(FindEntity $find): Response
    {
        $classroom = $find->findClassroom();
        $lesson = $find->findLesson();

        return $this->render('lesson/index.html.twig', [
            'lesson' => $lesson,
            'questionnaires' => $lesson->getQuestionnaires(),
            'classroom' => $classroom,
        ]);
    }

    /**
     * @Route("/list", name="list_lessons")
     */
    public function listLessons(FindEntity $find, Request $request): Response
    {
        $classroom_id = $request->query->get('classroom_id');
        $lessons = $find->findAllLessons();

        return $this->render('lesson/list.html.twig', [
            'lessons' => $lessons,
            'classroom_id' => $classroom_id,
        ]);
    }

    /**
     * @Route("/create", name="lesson_create")
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     */
    public function createLesson(Request $request, ClassroomRepository $repository): Response
    {
        $lesson = new Lesson();
        $classroom_id = $request->query->get('classroom_id');
        $classroom = $repository->findOneById($classroom_id);
        if (isset($classroom)) {
            $lesson->addClassroom($classroom);
        }

        // Add actual date and the user
        $lesson->setDateCreation(new \DateTime());
        $lesson->addUser($this->getUser());
        $lesson->setCreator($this->getUser()->getUsername());
        $form = $this->createForm(LessonType::class, $lesson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module ajouté avec succès.');

            if (isset($classroom)) {
                return $this->redirectToRoute(
                    'lesson_index',
                    [
                        'id' => $lesson->getId(),
                        'classroom' => $classroom_id,
                    ]
                );
            }

            return $this->redirectToRoute('toolbox_index');
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
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     *
     * @param \App\Entity\Lesson|null $lesson
     */
    public function editLesson(Lesson $lesson, Request $request): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Module modifiée avec succès.');

            return $this->redirectToRoute('toolbox_index');
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
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     */
    public function deleteLesson(Lesson $lesson, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$lesson->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimée avec succès.');
        }

        return $this->redirectToRoute('list_lesson');
    }

    /**
     * Add questionnaire direct to a lesson.
     *
     * @Route("/questionnaire/add", name="add_questionnaire_lesson")
     * @ParamConverter("questionnaire", class="\App\Entity\Questionnaire")
     */
    public function addQuestionnaireToLesson(
        LessonRepository $lessonRepo,
        QuestionnaireRepository $questionnaireRepo,
        ClassroomRepository $classroomRepo,
        Request $request
    ): RedirectResponse {
        // find lesson
        $lesson_id = $request->query->get('lesson');
        $lesson = $lessonRepo->findOneById($lesson_id);

        // find questionnaire
        $questionnaire_id = $request->query->get('questionnaire');
        $questionnaire = $questionnaireRepo->findOneById($questionnaire_id);

        // find classroom
        $classroom_id = $request->query->get('classroom');

        $lesson->addQuestionnaire($questionnaire);
        $this->em->persist($lesson);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté avec succès.');

        return $this->redirectToRoute(
            'lesson_index',
            [
                'id' => $lesson_id,
                'classroom' => $classroom_id,
            ]
        );
    }

    /**
     * @Route("/questionnaire/{id}/delete", name="delete_questionnaire_lesson", methods={"DELETE"})
     */
    public function deleteQuestionnaireFromLesson(LessonRepository $lessonRepo, QuestionnaireRepository $questionnaireRepo, Request $request): RedirectResponse
    {
        // find questionnaire
        $questionnaire_id = $request->attributes->get('id');
        $questionnaire = $questionnaireRepo->findOneById($questionnaire_id);

        // find lesson
        $lesson_id = $request->query->get('lesson_id');
        $lesson = $lessonRepo->findOneById($lesson_id);

        // find classroom
        $classroom_id = $request->query->get('classroom_id');

        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$lesson_id,
            $request->get('_token')
        )) {
            $lesson->removeQuestionnaire($questionnaire);
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Activité supprimé avec succès.');
        }

        return $this->redirectToRoute('lesson_index', [
            'id' => $lesson_id,
            'classroom_id' => $classroom_id,
        ]);
    }
}
