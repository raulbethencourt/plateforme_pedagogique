<?php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Class LessonController
 * This class manage the classrooms
 * @Route("/lesson")
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
     * @Route("/{id}", name="lesson_index")
     */
    public function index(): Response
    {
        return $this->render('lesson/index.html.twig', [
            'controller_name' => 'LessonController',
        ]);
    }

    /**
     * Lesson creation
     * @Route("/create", name="lesson_create")
     * @param Lesson|null $lesson
     * @param Request $request
     * @return Response
     */
    public function createLesson(?Lesson $lesson, Request $request): Response
    {
        // Check if lesson already exist
        if (!$lesson) {
            $lesson = new Lesson();
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
                'lesson_create',
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
}
