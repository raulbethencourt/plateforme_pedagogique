<?php

namespace App\Controller;

use App\Controller\Service\InvitationsController as Invitations;
use App\Controller\Service\NotificationsController as Notify;
use App\Entity\Classroom;
use App\Entity\Invite;
use App\Entity\Notification;
use App\Form\ClassroomType;
use App\Form\InviteType;
use App\Form\NotificationType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/classroom")
 */
class ClassroomController extends AbstractController
{
    private $em;

    private $find;

    private $request;

    private $notifications;

    private $invitations;

    private $breadCrumbs;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, Notify $notifications, Invitations $invitations, BreadCrumbs $breadCrumbs)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->notifications = $notifications;
        $this->invitations = $invitations;
        $this->breadCrumbs = $breadCrumbs;
    }

    /**
     * @Route("/new", name="classroom_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(): Response
    {
        $this->breadCrumbs->bcClassroom(null, 'new');

        $classroom = new Classroom();
        $classroom->addUser($this->getUser());
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe créée avec succès.');

            return $this->redirectToRoute('classroom_show', [
                'id' => $classroom->getId(),
            ]);
        }

        return $this->render('classroom/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="classroom_show", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN') or is_granted('ROLE_STUDENT')")
     */
    public function show(Classroom $classroom): Response
    {
        $this->breadCrumbs->bcClassroom($classroom, 'show');

        // here i handle notifications
        $notification = new Notification();
        $notification->setClassroom($classroom);
        $formNotify = $this->createForm(NotificationType::class, $notification);
        $this->notifications->notify($notification, $classroom, $formNotify);

        // here i handle invitations
        $invite = new Invite(); // We invite a new teacher or student
        $formInvite = $this->createForm(InviteType::class, $invite, ['user' => $this->getUser()]);
        $this->invitations->invitation($formInvite, $invite, $classroom);

        return $this->render('classroom/show.html.twig', [
            'notification' => $this->find->findNotification($classroom),
            'formInvite' => $formInvite->createView(),
            'formNotify' => $formNotify->createView(),
            'classroom' => $classroom,
            'extra' => $this->request->query->get('extra'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="classroom_edit", methods={"GET", "POST"})
     */
    public function edit(Classroom $classroom): Response
    {
        $this->breadCrumbs->bcClassroom($classroom, 'edit');

        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Classe modifiée avec succès.');

            return $this->redirectToRoute('classroom_show', [
                'id' => $classroom->getId(),
            ]);
        }

        return $this->render('classroom/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="classroom_delete", methods={"DELETE"})
     */
    public function delete(Classroom $classroom): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classroom->getId(), $this->request->request->get('_token'))) {
            $this->em->remove($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe supprimée avec succès.');
        }

        return $this->redirectToRoute('user_show');
    }

    /**
     * @Route("/{id}/user_delete", name="classroom_user_remove", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function removeUserFromClassroom(Classroom $classroom): Response
    {
        // find user
        $user = $this->find->findUser();
        $classroom->removeUser($user);

        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur·rice supprimé·e de la classe avec succès.');

        return $this->redirectToRoute('classroom_show', [
            'id' => $classroom->getId(),
        ]);
    }

    /**
     * @Route("/{id}/add_lesson", name="classroom_lesson_add", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function addLessonToClass(Classroom $classroom): RedirectResponse
    {
        // find lesson
        $lesson = $this->find->findLesson();
        $classroom->addLesson($lesson);
        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté à la classe avec succès.');

        return $this->redirectToRoute('classroom_show', [
            'id' => $classroom->getId(),
        ]);
    }

    /**
     * @Route("/{id}/lesson_remove", name="classroom_lesson_remove", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function removeLessonFromClass(Classroom $classroom): Response
    {
        // find lesson
        $lesson = $this->find->findLesson();
        // Check the token
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $this->request->get('_token'))) {
            $classroom->removeLesson($lesson);
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimé de la classe avec succès.');
        }

        return $this->redirectToRoute('classroom_show', [
            'id' => $classroom->getId(),
        ]);
    }

    /**
     * @Route("/{id}/link_add", name="classroom_link_add", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function addLinkToClass(Classroom $classroom): RedirectResponse
    {
        $link = $this->find->findLink();
        $classroom->addLink($link);
        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Lien ajouté à la classe avec succès.');

        return $this->redirectToRoute('classroom_show', [
            'id' => $classroom->getId(),
        ]);
    }

    /**
     * @Route("/{id}/link_remove", name="classroom_link_remove", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function removeLinkFromClass(Classroom $classroom): Response
    {
        $link = $this->find->findLink();
        $extra = $this->request->query->get('extra');
        // Check the token
        if ($this->isCsrfTokenValid('delete'.$link->getId(), $this->request->get('_token'))) {
            $classroom->removeLink($link);
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Lien supprimé de la classe avec succès.');
        }

        return $this->redirectToRoute('classroom_show', [
            'id' => $classroom->getId(),
            'extra' => $extra
        ]);
    }
}
