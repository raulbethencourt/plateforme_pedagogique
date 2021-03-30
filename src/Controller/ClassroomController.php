<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Invite;
use App\Form\InviteType;
use App\Entity\Classroom;
use App\Entity\Notification;
use App\Form\NotificationType;
use App\invitation\Invitation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class ClassroomController
 * This class manage the classrooms
 * @Route("/classroom")
 * @package App\Controller
 */
class ClassroomController extends AbstractController
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
     * This method shows the students and teacher that belongs to the classroom
     * and It allows us to invite new Teachers or students
     * @Route("/{id}", name="classroom_index")
     * @IsGranted ("ROLE_USER")
     * @param Classroom $classroom
     * @param Request $request
     * @param Invitation $invitation
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function index(Classroom $classroom, Request $request, Invitation $invitation, UserRepository $user): Response
    {
        $notification = new Notification(); // I create the admin notification
        $notification->setClassroom($classroom);
        $formNotify = $this->createForm(NotificationType::class, $notification);
        $this->notify($classroom, $request, $formNotify, $notification);

        $invite = new Invite(); // We invite a new teacher or student    
        $formInvite = $this->createForm(InviteType::class, $invite);
        $this->invite($classroom, $request, $invitation, $user, $formInvite, $invite);

        return $this->render(
            'user/classroom/index.html.twig',
            [
                'formInvite' => $formInvite->createView(),
                'formNotify' => $formNotify->createView(),
                'classroom' => $classroom,
                'students' => $classroom->getStudents(),
                'teachers' => $classroom->getTeachers(),
            ]
        );
    }

    /**
     * @Route ("/user/{id}/{classroom}/delete", name="user_user_delete", methods={"DELETE"})
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function deleteUser(User $user, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete' . $user->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur supprimée avec succès.');
        }

        return $this->redirectToRoute('classroom_index', ['id' => $request->attributes->get('classroom')]);
    }

    /**
     * with this function I invite different users
     * @param \App\Entity\Classroom $classroom
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\invitation\Invitation $invitation
     * @param \App\Repository\UserRepository $user
     * @param mixed $form
     * @param \App\Entity\Invite $invite
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function invite(Classroom $classroom, Request $request, Invitation $invitation, UserRepository $user, $form, Invite $invite): RedirectResponse
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if user is in the data base already
            $userAlready = $user->findOneBy([
                "name" => $invite->getName(),
                "surname" => $invite->getSurname()
            ]);
            if (isset($userAlready)) {
                $invitation->invite($invite, $classroom, $userAlready);
            } else {
                $invitation->invite($invite, $classroom);
            }

            $this->addFlash('success', 'Votre invitation a bien été envoyée.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom->getId()
        ]);
    }

    /**
     * with this function the admin can send a notification to classroom students
     * @param \App\Entity\Classroom $classroom
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param [type] $formNotify
     * @param \App\Entity\Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function notify(Classroom $classroom, Request $request, $formNotify, Notification $notification): RedirectResponse
    {
        $formNotify->handleRequest($request);

        if ($formNotify->isSubmitted() && $formNotify->isValid()) {
            $this->em->persist($notification);
            $this->em->flush();

            $this->addFlash('success', 'Notification ajouté avec succès.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom->getId()
        ]);
    }
}
