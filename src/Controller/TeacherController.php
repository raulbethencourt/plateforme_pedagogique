<?php

namespace App\Controller;

use App\Form\EditTeacherType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher', name: 'teacher_')]
class TeacherController extends AbstractController
{
    private $breadCrumbs;
    private $em;

    public function __construct(BreadCrumbs $breadCrumbs, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->breadCrumbs = $breadCrumbs;
    }

    #[Route('/teacher', name: 'show')]
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

    #[Route(
        '/profile',
        name: 'profile',
        methods: ['GET']
    )]
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

    #[Route(
        '/profile/edit',
        name: 'edit_profile',
        methods: ['GET', 'POST']
    )]
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
            $this->em->persist($teacher);
            $this->em->flush();
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
