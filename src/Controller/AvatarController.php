<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AvatarController extends AbstractController
{
    /**
     * @Route("/student/avatar", name="edit_student_avatar")
     * @Route("/teacher/avatar", name="edit_teacher_avatar")
     * @Route("/user/avatar", name="edit_user_avatar")
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addRouteItem('Accueil', 'user_index');
        switch ($this->getUser()->getRoles()[0]) {
            case 'ROLE_TEACHER':
                $breadcrumbs->addRouteItem('Profile', 'teacher_profile');
                break;
            case 'ROLE_STUDENT':
                $breadcrumbs->addRouteItem('Profile', 'student_profile');
                break;
            default:
                $breadcrumbs->addRouteItem('Profile', 'user_profile');
        }

        $breadcrumbs->addRouteItem('Avatar', 'edit_student_avatar');
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
            'student' => $this->getUser(),
            'teacher' => $this->getUser(),
            'user' => $this->getUser(),
        ]);
    }
}
