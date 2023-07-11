<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BreadCrumbsService as BreadCrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvatarController extends AbstractController
{
    /**
     * @Route("/student/avatar", name="student_edit_avatar")
     * @Route("/teacher/avatar", name="teacher_edit_avatar")
     * @Route("/user/avatar", name="user_edit_avatar")
     */
    public function new(Request $request, BreadCrumbs $breadcrumbs): Response
    {
        $breadcrumbs->bcAvatar();

        $avatar = $this->getUser()->getAvatar();
        // Check if the image already exist
        if (!$avatar) {
            $avatar = new Avatar();
            $avatar->setUpdatedAt(new \DateTime());
            $avatar->setUser($this->getUser());
        }

        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($avatar);
            $em->flush();
            $this->addFlash('success', 'Avatar ajouté avec succès.');

            switch ($this->getUser()->getRoles()[0]) {
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
