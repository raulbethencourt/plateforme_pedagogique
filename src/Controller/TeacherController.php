<?php

namespace App\Controller;

use App\Service\FindEntity;
use App\Form\EditTeacherType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class TeacherController
 * This class manage questionnaire creation by the teachers.
 *
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
{
    /**
     * @Route("/", name="teacher_index")
     */
    public function index(): ResponseAlias
    {
        $teacher = $this->getUser();

        return $this->render(
            'teacher/index.html.twig',
            [
                'questionnaires' => $teacher->getClassroomsTeacher(),
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
    public function editProfile(Request $request, FindEntity $find): Response
    {
        $teacher_name = $request->query->get('username');
        if (isset($teacher_name)) {
            $teacher = $find->findTeacherByUsername($teacher_name);
        } else {
            $teacher = $this->getUser();
        }

        $form = $this->createForm(EditTeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($teacher);
            $entityManager->flush();
            $this->addFlash('success', 'Profil édité avec succès.');
            
            if (isset($teacher_name)) {
                return $this->redirectToRoute('user_list');
            }
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
