<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    private $breadCrumbs;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, BreadCrumbs $breadCrumbs)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/", name="lesson_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function index(LessonRepository $lessonRepo, PaginatorInterface $paginator): Response
    {
        $request = $this->request->query;
        $classroom_id = $request->get('classroom_id');
        $list = $request->get('list');
        $user = $this->getUser();

        if ('ROLE_TEACHER' === $user->getRoles()[0]) {
            $lessons = $lessonRepo->findByVisibilityOrCreator(true, $user->getUsername());
        } else {
            $lessons = $lessonRepo->findAll();
        }

        $this->breadCrumbs->bcLesson(null, 'index', $classroom_id, $list, null);

        $lessons = $paginator->paginate(
            $lessons,
            $request->getInt('page', 1),
            10
        );

        $lessons->setCustomParameters([
            'align' => 'center',
            'rounded' => true,
        ]);

        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessons,
            'classroom_id' => $classroom_id,
            'list' => $list,
        ]);
    }

    /**
     * @Route("/new", name="lesson_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function new(): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');
        $classroom = $this->find->findClassroom();

        $this->breadCrumbs->bcLesson(null, 'new', $classroom_id, null, null);

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
                    'classroom_id' => $classroom->getId(),
                    'lonely' => true,
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
        $request = $this->request->query;
        $classroom_id = $request->get('classroom_id');
        $lonely = $request->get('lonely');
        $list = $request->get('list');

        $this->breadCrumbs->bcLesson($lesson, 'show', $classroom_id, $list, $lonely);

        $user = $this->getUser()->getRoles()[0];
        switch ($user) {
            case 'ROLE_ADMIN':
            case 'ROLE_SUPER_ADMIN':
            case 'ROLE_TEACHER':
                $questionnaires = $lesson->getQuestionnaires();
                break;
            default:
                $questionnaires = [];
                foreach ($lesson->getQuestionnaires() as $questionnaire) {
                    if ($questionnaire->getPlayable() && $questionnaire->isPlayable()) {
                        $questionnaires[] = $questionnaire;
                    }
                }
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'questionnaires' => $questionnaires,
            'classroom_id' => $classroom_id,
            'list' => $list,
            'lonely' => $lonely,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function edit(Lesson $lesson): Response
    {
        $classroom_id = $this->request->query->get('classroom_id');

        $this->breadCrumbs->bcLesson($lesson, 'edit', $classroom_id, true, null);

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Module modifiée avec succès.');

            if ($classroom_id) {
                return $this->redirectToRoute('lesson_index', [
                    'classroom_id' => $classroom_id,
                    'list' => true,
                ]);
            }

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

        $classroom_id = $this->request->query->get('classroom_id');
        if (isset($classroom_id)) {
            return $this->redirectToRoute('lesson_index', [
                'classroom_id' => $classroom_id,
                'list' => true,
            ]);
        }

        return $this->redirectToRoute('lesson_index');
    }

    /**
     * @Route("/{id}/add_questionnaire", name="lesson_questionnaire_add", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function addQuestionnaireToLesson(Lesson $lesson): Response
    {
        $questionnaire = $this->find->findQuestionnaire();
        $lesson->addQuestionnaire($questionnaire);
        $this->em->persist($lesson);
        $this->em->flush();
        $this->addFlash('success', 'Questionnare ajouté avec succès.');

        return $this->redirectToRoute('lesson_show', [
            'id' => $lesson->getId(),
            'classroom' => $this->request->query->get('classroom'),
        ]);
    }

    /**
     * @Route("/{id}/questionnaire_remove", name="lesson_questionnaire_remove", methods={"DELETE"})
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
