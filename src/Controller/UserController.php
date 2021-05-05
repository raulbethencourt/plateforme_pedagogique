<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Form\InviteType;
use App\Form\EditUserType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Service\InvitationsController;
use App\Service\BreadCrumbsService as BreadCrumbs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    private $breadCrumbs;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, BreadCrumbs $breadCrumbs)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/", name="user_show")
     */
    public function show(InvitationsController $invitation): Response
    {
        $user = $this->getUser();
        if ('ROLE_ADMIN' === $user->getRoles()[0]) {
            $classrooms = $user->getClassrooms();
        } else {
            $classrooms = $this->find->findAllClassrooms();
        }
        $admins = $this->find->findUsersByRole('ROLE_ADMIN');

        // admin invitation
        $invite = new Invite();
        $form = $this->createForm(InviteType::class, $invite, ['user' => $user]);
        $invitation->invitation($form, $invite);

        return $this->render(
            'user/show.html.twig',
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
    public function listUsers(PaginatorInterface $paginator): Response
    {
        $type = $this->request->query->get('users');

        if ('teachers' === $type) {
            $users = $this->find->findUsersByRole('ROLE_TEACHER');
            $this->breadCrumbs->bcListUsers($type);
        } else {
            $users = $this->find->findUsersByRole('ROLE_STUDENT');
            $this->breadCrumbs->bcListUsers($type);
        }

        $users = $paginator->paginate(
                $users,
                $this->request->query->getInt('page', 1),
                10
            );

        $users->setCustomParameters([
            'align' => 'center',
            'rounded' => true,
        ]);

        return $this->render('user/list.html.twig', [
            'users' => $users,
            'type' => $type,
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

        return $this->redirectToRoute('user_show');
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(): Response
    {
        $this->breadCrumbs->bcProfile('user', false);

        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/profile/edit", name="user_edit_profile")
     */
    public function editProfile(): Response
    {
        $this->breadCrumbs->bcProfile('user', true);

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

        return $this->render('user/edit-profile.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }
}
