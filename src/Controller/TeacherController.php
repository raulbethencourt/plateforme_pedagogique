<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\EditTeacherType;
use App\Repository\QuestionnaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeacherController
 * This class manage questionnaire creation by the teachers.
 *
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
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
     * @Route("/", name="teacher_index")
     *
     * @param QuestionnaireRepository $repository
     */
    public function index(): ResponseAlias
    {
        $teacher = $this->getUser();

        return $this->render(
            'teacher/index.html.twig',
            [
                'questionnaires' => $teacher->getClassrooms(),
                'teacher' => $teacher,
            ]
        );
    }

    /**
     * @Route("/profile", name="teacher_profile")
     */
    public function teacherProfile(): ResponseAlias
    {
        return $this->render(
            'teacher/profile.html.twig',
            [
                'teacher' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/profile/edit", name="edit_teacher")
     */
    public function editProfile(Request $request): Response
    {
        $teacher = $this->getUser();

        $form = $this->createForm(EditTeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($teacher);
            $entityManager->flush();

            $this->addFlash('success', 'Profil édité avec succès.');

            return $this->redirectToRoute('teacher_profile');
        }

        return $this->render(
            'teacher/edit-profile.html.twig',
            [
                'editForm' => $form->createView(),
                'teacher' => $this->getUser(),
            ]
        );
    }
}
