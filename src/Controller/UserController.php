<?php

namespace App\Controller;

use App\Controller\Service\InvitationsController;
use App\Entity\Classroom;
use App\Entity\Invite;
use App\Form\ClassroomType;
use App\Form\EditUserType;
use App\Form\InviteType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Route("/", name="user_index")
     */
    public function index(InvitationsController $invitation): Response
    {
        $classrooms = $this->find->findAllClassrooms();
        $admins = $this->find->findUsersByRole('ROLE_ADMIN');
        $user = $this->getUser();

        // admin invitation
        $invite = new Invite();
        $form = $this->createForm(InviteType::class, $invite, ['user' => $this->getUser()]);
        $invitation->invitation($form, $invite);

        return $this->render(
            'user/index.html.twig',
            [
                'admins' => $admins,
                'classrooms' => $classrooms,
                'user' => $user,
                'form' => $form->createView(),
            ]
            );
    }

    /**
     * @Route("/list", name="user_list")
     */
    public function listUser(): Response
    {
        $teachers = $this->find->findUsersByRole('ROLE_TEACHER');
        $students = $this->find->findUsersByRole('ROLE_STUDENT');

        return $this->render('user/list.html.twig', [
            'teachers' => $teachers,
            'students' => $students,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="user_delete", methods={"DELETE"})
     */
    public function deleteUser(): RedirectResponse
    {
        $user = $this->find->findUser();
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$user->getId(),
            $this->request->get('_token')
        )) {
            $this->em->remove($user);
            $this->em->flush();
            if ('ROLE_TEACHER' === $user->getRoles()[0] || 'ROLE_STUDENT' === $user->getRoles()[0]) {
                $this->addFlash('success', 'Utilisateur supprimée avec succès.');
            } else {
                $this->addFlash('success', 'Administrateur supprimée avec succès.');
            }
        }

        if ('ROLE_TEACHER' === $user->getRoles()[0] || 'ROLE_STUDENT' === $user->getRoles()[0]) {
            return $this->redirectToRoute('user_list');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/classroom/create", name="user_classroom_create")
     */
    public function createClassroom(): Response
    {
        $classroom = new Classroom();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe créée avec succès.');

            return $this->redirectToRoute(
                'classroom_index',
                [
                    'id' => $classroom->getId(),
                ]
            );
        }

        return $this->render(
            'classroom/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/classroom/{id}/edit", name="user_classroom_edit", methods={"GET", "POST"})
     */
    public function editClassroom(): Response
    {
        $classroom = $this->find->findClassroom();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Classe modifiée avec succès.');

            return $this->redirectToRoute(
                'classroom_index',
                [
                    'id' => $classroom->getId(),
                ]
            );
        }

        return $this->render(
            'classroom/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/classroom/{id}/delete", name="user_classroom_delete", methods={"DELETE"})
     */
    public function deleteClassroom(): RedirectResponse
    {
        $classroom = $this->find->findClassroom();
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$classroom->getId(),
            $this->request->get('_token')
        )) {
            $this->em->remove($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe supprimée avec succès.');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function userProfile(): Response
    {
        return $this->render(
            'user/profile.html.twig',
            [
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/profile/edit", name="edit_user")
     */
    public function editProfile(): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil édité avec succès.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'user/edit-profile.html.twig',
            [
                'editForm' => $form->createView(),
            ]
        );
    }
}
