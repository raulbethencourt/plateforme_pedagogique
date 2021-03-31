<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class LessonController
 * This class manage the lessons
 * @Route("/lesson")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
 * @package App\Controller
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
     * @Route("/{id}", name="lesson_index", requirements={"id":"\d+"})
     * @param \App\Entity\Lesson $lesson
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Lesson $lesson): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Lesson creation
     * @Route("/create", name="lesson_create")
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createLesson(Request $request, ClassroomRepository $repository): Response
    {
        $lesson = new Lesson();
        $classroom = $repository->findOneById($request->query->get('classroom'));
        if ($classroom) {
            $lesson->addClassroom($classroom);
        }

        // Add actual date and the user
        $lesson->setDateCreation(new \DateTime());
        $lesson->addUser($this->getUser());
        $form = $this->createForm(LessonType::class, $lesson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module ajouté avec succès.');

            return $this->redirectToRoute(
                'lesson_index',
                [
                    'id' => $lesson->getId(),
                ]
            );
        }

        return $this->render(
            'lesson/new.html.twig',
            [
                'lesson' => $lesson,
                'form' => $form->createView(),
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route ("/lesson/{id}/edit", name="lesson_edit", methods={"GET","POST"})
     * @param \App\Entity\Lesson $lesson
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editLesson(Lesson $lesson, Request $request): RedirectResponse
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Module modifiée avec succès.');

            return $this->redirectToRoute(
                'classroom_index',
                [
                    'id' => $request->query->get('classroom')
                ]
            );
        }

        return $this->render(
            'lesson/edit.html.twig',
            [
                'lesson' => $lesson,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route ("/lesson/{id}/delete", name="lesson_delete", methods={"DELETE"})
     * @param \App\Entity\Lesson $lesson
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteLesson(Lesson $lesson, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete' . $lesson->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($lesson);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimée avec succès.');
        }

        return $this->redirectToRoute(
            'classroom_index',
            [
                'id' => $request->query->get('classroom')
            ]
        );
    }
}
