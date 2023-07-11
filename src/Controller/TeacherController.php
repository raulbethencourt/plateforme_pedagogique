<?php

namespace App\Controller;

use App\Form\EditTeacherType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
{
    private $breadCrumbs;

    public function __construct(BreadCrumbs $breadCrumbs)
    {
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/", name="teacher_show")
     */
    public function show(): Response
    {
        $teacher = $this->getUser();

        return $this->render(
            'teacher/show.html.twig',
            [
                'teacher' => $teacher,
                'classrooms' => $teacher->getClassrooms(),
            ]
        );
    }

    /**
     * @Route("/profile", name="teacher_profile")
     */
    public function profile(): Response
    {
        $this->breadCrumbs->bcProfile(false, false);

        return $this->render(
            'teacher/profile.html.twig',
            [
                'teacher' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/profile/edit", name="teacher_edit_profile")
     */
    public function editProfile(Request $request, FindEntity $find): Response
    {
        if ($request->query->get('list_profile_edit')) {
            $this->breadCrumbs->bcListUsers('teachers', $request->query->get('list_profile_edit'));
        } else {
            $this->breadCrumbs->bcProfile(true);
        }

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
                return $this->redirectToRoute('user_list', [
                    'type' => 'teachers',
                ]);
            }

            return $this->redirectToRoute('teacher_profile');
        }

        return $this->render(
            'teacher/edit-profile.html.twig',
            [
                'editForm' => $form->createView(),
                'teacher' => $this->getUser(),
                'list_profile_edit' => $request->query->get('list_profile_edit'),
            ]
        );
    }
}
