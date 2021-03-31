<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Invite;
use App\Form\InviteType;
use App\Entity\Classroom;
use App\Form\EditUserType;
use App\Form\ClassroomType;
use App\invitation\Invitation;
use App\Repository\UserRepository;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
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
     * @Route ("/", name="user_index")
     * @param  ClassroomRepository  $repository
     * @return ResponseAlias
     */
    public function index(ClassroomRepository $classroomRepository, UserRepository $adminsRepository,  Request $request, Invitation $invitation): Response
    {
        $classrooms = $classroomRepository->findAll();
        $admins = $adminsRepository->findByRoleAdmin('ROLE_ADMIN');
        $user = $this->getUser();
        $invite = new Invite(); // We invite a new teacher or student

        $form = $this->createForm(InviteType::class, $invite);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->invite($invite);

            $this->addFlash('success', 'Votre invitation a bien été envoyée.');
            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/index.html.twig',
            [
                'admins' => $admins,
                'classrooms' => $classrooms,
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route ("/{id}/delete", name="user_admin_delete", methods={"DELETE"})
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function deleteAdmin(User $admin, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete' . $admin->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($admin);
            $this->em->flush();
            $this->addFlash('success', 'Administrateur supprimée avec succès.');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route ("/classroom/create", name="user_classroom_create")
     * @param Request $request
     * @param Classroom|null $classroom
     * @return RedirectResponse|ResponseAlias
     */
    public function createClassroom(Request $request, Classroom $classroom = null)
    {
        // Check if the classroom already exist
        if (!$classroom) {
            $classroom = new Classroom();
        }

        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe créée avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/classroom/create.html.twig',
            [
                'classrooms' => $classroom,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/classroom/{id}/edit", name="user_classroom_edit", methods={"GET","POST"})
     * @param  Classroom  $classroom
     * @param  Request  $request
     * @return RedirectResponse|ResponseAlias
     */
    public function editClassroom(Classroom $classroom, Request $request)
    {
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Classe modifiée avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/classroom/edit.html.twig',
            [
                'classroom' => $classroom,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/classroom/{id}/delete", name="user_classroom_delete", methods={"DELETE"})
     * @param  Classroom  $classroom
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function deleteClassroom(Classroom $classroom, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete' . $classroom->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe supprimée avec succès.');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route ("/profile", name="user_profile")
     */
    public function userProfile()
    {
        return $this->render(
            'user/profile.html.twig',
            [
                'user' => $this->getUser(),
            ]
        );
    }



    /**
     * @Route ("/profile/edit", name="edit_user")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

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
