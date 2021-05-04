<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    private $breadCrumbs;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, Breadcrumbs $breadCrumbs)
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
        $user = $this->getUser();

        if ('ROLE_TEACHER' === $user->getRoles()[0]) {
            $lessons = $lessonRepo->findByVisibilityOrCreator(true, $user->getUsername());
            $this->breadCrumbs->addRouteItem('Acueille', 'teacher_show');
        } else {
            $lessons = $lessonRepo->findAll();
            $this->breadCrumbs->addRouteItem('Acueille', 'user_show');
        }

        if ($request->get('classroom_id')) {
            $classroom = $this->find->findClassroom();
            $this->breadCrumbs
                ->addRouteItem('Classe',
                    'classroom_show',
                    ['id' => $classroom->getId()]
                )
                ->addRouteItem('Créer un Module', 'lesson_new', ['classroom_id' => $classroom->getId()])
                ->addRouteItem('Modules', 'lesson_index')
            ;
        } else {
            $this->breadCrumbs->addRouteItem('Modules', 'lesson_index');
        }

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
            'classroom_id' => $request->get('classroom_id'),
            'list' => $request->get('list'),
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

        if ('ROLE_TEACHER' === $this->getUser()->getRoles()[0]) {
            $this->breadCrumbs->addRouteItem('Acueille', 'teacher_show');
        } else {
            $this->breadCrumbs->addRouteItem('Acueille', 'user_show');
        }

        if (isset($classroom)) {
            $lesson->addClassroom($classroom);
            $this->breadCrumbs->addRouteItem('Classe', 'classroom_show', ['id' => $classroom->getId()]);
        } else {
            $this->breadCrumbs->addRouteItem('Modules', 'lesson_index');
        }
        $this->breadCrumbs->addRouteItem('Créer Module', 'lesson_new');

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
        $classroom = $this->find->findClassroom();

        switch ($this->getUser()->getRoles()[0]) {
            case 'ROLE_TEACHER':
                $this->breadCrumbs->addRouteItem('Accueil', 'teacher_show');
                break;
            case 'ROLE_STUDENT':
                $this->breadCrumbs->addRouteItem('Accueil', 'student_show');
                break;
            default:
                $this->breadCrumbs->addRouteItem('Accueil', 'user_show');
                break;
        }

        if ($request->get('classroom_id') && $request->get('list')) {
            $this->breadCrumbs
                ->addRouteItem('Classe',
                    'classroom_show',
                    ['id' => $classroom->getId()]
                )
                ->addRouteItem('Créer un Module', 'lesson_new', ['classroom_id' => $classroom->getId()])
                ->addRouteItem('Modules', 'lesson_index', [
                    'classroom_id' => $classroom->getId(),
                    'list' => $request->get('list'),
                ])
                ->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
            ;
        } elseif ($request->get('lonely')) {
            $this->breadCrumbs
                ->addRouteItem('Classe',
                    'classroom_show',
                    ['id' => $classroom->getId()]
                )
                ->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
            ;
        } else {
            $this->breadCrumbs
                ->addRouteItem('Modules', 'lesson_index')
                ->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
            ;
        }

        $user = $this->getUser()->getRoles()[0];
        if ('ROLE_ADMIN' === $user || 'ROLE_TEACHER' === $user || 'ROLE_SUPER_ADMIN' === $user) {
            $questionnaires = $lesson->getQuestionnaires();
        } else {
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
            'classroom_id' => $request->get('classroom_id'),
            'list' => $request->get('list'),
            'lonely' => $request->get('lonely'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function edit(Lesson $lesson): Response
    {
        if ('ROLE_TEACHER' === $this->getUser()->getRoles()[0]) {
            $this->breadCrumbs->addRouteItem('Acueille', 'teacher_show');
        } else {
            $this->breadCrumbs->addRouteItem('Acueille', 'user_show');
        }

        $classroom_id = $this->request->query->get('classroom_id');
        if ($classroom_id) {
            $classroom = $this->find->findClassroom();
            $this->breadCrumbs
                ->addRouteItem('Classe',
                    'classroom_show',
                    ['id' => $classroom->getId()]
                )
                ->addRouteItem('Créer un Module', 'lesson_new', ['classroom_id' => $classroom->getId()])
                ->addRouteItem('Modules', 'lesson_index', ['classroom_id' => $classroom->getId()])
                ->addRouteItem('Editer un Module', 'lesson_edit', ['id' => $lesson->getId()])
            ;
        } else {
            $this->breadCrumbs
                ->addRouteItem('Modules', 'lesson_index')
                ->addRouteItem('Editer un Module', 'lesson_edit', ['id' => $lesson->getId()])
            ;
        }

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
