<?php

namespace App\Controller;

use App\Controller\Service\InvitationsController as Invitations;
use App\Controller\Service\NotificationsController as Notify;
use App\Entity\Invite;
use App\Entity\Notification;
use App\Form\InviteType;
use App\Form\NotificationType;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//TODO links section

/**
 * Class ClassroomController
 * This class manage the classrooms.
 *
 * @Route("/classroom")
 */
class ClassroomController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $find;

    private $request;

    private $notifications;

    private $invitations;

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, Notify $notifications, Invitations $invitations)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->notifications = $notifications;
        $this->invitations = $invitations;
    }

    /**
     * This method shows the students and teacher that belongs to the classroom
     * and It allows us to invite new Teachers or students.
     *
     * @Route("/{id}", name="classroom_index", requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN') or is_granted('ROLE_STUDENT')")
     */
    public function index(): Response
    {
        $classroom = $this->find->findClassroom();

        // here i handle notifications
        $notification = new Notification();
        $notification->setClassroom($classroom);
        $formNotify = $this->createForm(NotificationType::class, $notification);
        $this->notifications->notify($notification, $classroom, $formNotify);

        // here i handle invitations
        $invite = new Invite(); // We invite a new teacher or student
        $formInvite = $this->createForm(InviteType::class, $invite, ['user' => $this->getUser()]);
        $this->invitations->invitation($formInvite, $invite, $classroom);

        return $this->render(
            'classroom/index.html.twig',
            [
                'notification' => $this->find->findNotification($classroom),
                'formInvite' => $formInvite->createView(),
                'formNotify' => $formNotify->createView(),
                'classroom' => $classroom,
                'students' => $classroom->getStudents(),
                'teachers' => $classroom->getTeachers(),
                'lessons' => $classroom->getLessons(),
            ]
            );
    }

    /**
     * @Route("/user/{id}/delete", name="delete_user_classroom", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function deleteUserFromClassroom(): RedirectResponse
    {
        // find classroom
        $classroom = $this->find->findClassroom();
        // find user
        $user = $this->find->findUser();

        if ('ROLE_STUDENT' === $user->getRoles()[0]) {
            $classroom->removeStudent($user);
        } else {
            $classroom->removeTeacher($user);
        }

        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur supprimée de la classe avec succès.');

        return $this->redirectToRoute(
            'classroom_index',
            [
                'id' => $classroom->getId(),
            ]
        );
    }

    /**
     * Add lesson direct to a class.
     *
     * @Route("/add", name="add_lesson_classroom")
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function addLessonToClass(): RedirectResponse
    {
        // find lesson
        $lesson = $this->find->findLesson();
        // find classroom
        $classroom = $this->find->findClassroom();

        $classroom->addLesson($lesson);
        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté avec succès.');

        return $this->redirectToRoute(
            'classroom_index',
            [
                'id' => $classroom->getId(),
            ]
        );
    }

    /**
     * @Route("/lesson/{id}/delete", name="delete_lesson_classroom", methods={"DELETE"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function deleteLessonFromClass(): RedirectResponse
    {
        // find lesson
        $lesson = $this->find->findLesson();
        // find classroom
        $classroom = $this->find->findClassroom();

        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$lesson->getId(),
            $this->request->get('_token')
        )) {
            $classroom->removeLesson($lesson);
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimé avec succès.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom->getId(),
        ]);
    }
}
