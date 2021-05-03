<?php

namespace App\Controller;

use App\Controller\Service\InvitationsController;
use App\Entity\Invite;
use App\Form\EditUserType;
use App\Form\InviteType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

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

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, Breadcrumbs $breadCrumbs)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->breadCrumbs = $breadCrumbs->addRouteItem('Accueil', 'user_index');
    }

    /**
     * @Route("/", name="user_index")
     */
    public function index(InvitationsController $invitation): Response
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
    public function listUser(PaginatorInterface $paginator): Response
    {
        $type = $this->request->query->get('users');

        if ('teachers' === $type) {
            $users = $this->find->findUsersByRole('ROLE_TEACHER');
            $this->breadCrumbs->addRouteItem('formateurs', 'user_list');
        } else {
            $users = $this->find->findUsersByRole('ROLE_STUDENT');
            $this->breadCrumbs->addRouteItem('apprenantes', 'user_list');
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

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function userProfile(): Response
    {
        $this->breadCrumbs->addRouteItem('Profile', 'user_profile');
        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/profile/edit", name="edit_user")
     */
    public function editProfile(): Response
    {
        $this->breadCrumbs
            ->addRouteItem('Profile', 'user_profile')
            ->addRouteItem('Editer Profile', 'edit_user');

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
