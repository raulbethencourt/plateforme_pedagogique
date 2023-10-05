<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    #[Route(
        '/student/avatar',
        name: 'student_edit_avatar',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/teacher/avatar',
        name: 'teacher_edit_avatar',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/user/avatar',
        name: 'user_edit_avatar',
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        BreadCrumbs $breadcrumbs,
        EntityManagerInterface $em
    ): Response {
        $breadcrumbs->bcAvatar();

        $avatar = $this->getUser()->getAvatar();
        $user = $this->getUser();
        // Check if the image already exist
        if (!$avatar) {
            $avatar = new Avatar();
            $avatar->setUser($user);
        }

        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avatar);
            $em->flush();
            $this->addFlash('success', 'Avatar ajouté avec succès.');

            switch ($user->getRoles()[0]) {
                case 'ROLE_STUDENT':
                    return $this->redirectToRoute('student_profile');
                case 'ROLE_TEACHER':
                    return $this->redirectToRoute('teacher_profile');
                default:
                    return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render('avatar/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
