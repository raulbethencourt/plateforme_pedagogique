<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Entity\Lesson;
use App\Form\InviteType;
use App\Service\FindEntity;
use App\Entity\Notification;
use App\Service\Invitations;
use App\Form\NotificationType;
use App\invitation\Invitation;
use App\Service\Notifications;
use Symfony\Component\Form\Form;
use App\Repository\UserRepository;
use App\Repository\LessonRepository;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    public function __construct(EntityManagerInterface $em, FindEntity $find, RequestStack $requestStack, Notifications $notifications, Invitations $invitations)
    {
        $this->em = $em;
        $this->find = $find;
        $this->request = $requestStack;
        $this->notifications = $notifications;
        $this->invitations = $invitations;
    }

    /**
     * This method shows the students and teacher that belongs to the classroom
     * and It allows us to invite new Teachers or students.
     *
     * @Route("/{id}", name="classroom_index", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(
        UserRepository $user,
        NotificationRepository $notificationRepository
    ): Response {
        $classroom = $this->find->findClassroom();
        $notification = new Notification(); // I create the admin notification
        $notification->setClassroom($classroom);
        $formNotify = $this->createForm(NotificationType::class, $notification);
        $this->notifications->notify($classroom, $this->request->getCurrentRequest(), $formNotify, $notification, $notificationRepository);

        $invite = new Invite(); // We invite a new teacher or student
        $formInvite = $this->createForm(InviteType::class, $invite);
        $this->invitations->invite($classroom, $request, $invitation, $user, $formInvite, $invite);

        return $this->render(
            'classroom/index.html.twig',
            [
                'notification' => $notificationRepository->findOneBy(['classroom' => $classroom]),
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
     */
    public function deleteUserFromClassroom(UserRepository $userRepo, ClassroomRepository $classroomRepo, Request $request): RedirectResponse
    {
        // find classroom
        $classroom_id = $request->query->get('classroom_id');
        $classroom = $classroomRepo->findOneById($classroom_id);

        // find user
        $user_id = $request->attributes->get('id');
        $user = $userRepo->findOneById($user_id);

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
                'id' => $classroom_id,
            ]
        );
    }

    /**
     * Add lesson direct to a class.
     *
     * @Route("/add", name="add_lesson_classroom")
     * @ParamConverter("lesson", class="\App\Entity\Lesson")
     */
    public function addLessonToClass(LessonRepository $lessonRepo, ClassroomRepository $classroomRepo, Request $request): RedirectResponse
    {
        // find lesson
        $lesson_id = $request->query->get('lesson');
        $lesson = $lessonRepo->findOneById($lesson_id);

        // find classroom
        $classroom_id = $request->query->get('classroom');
        $classroom = $classroomRepo->findOneById($classroom_id);

        $classroom->addLesson($lesson);
        $this->em->persist($classroom);
        $this->em->flush();
        $this->addFlash('success', 'Module ajouté avec succès.');

        return $this->redirectToRoute(
            'classroom_index',
            [
                'id' => $classroom_id,
            ]
        );
    }

    /**
     * @Route("/lesson/{id}/delete", name="delete_lesson_classroom", methods={"DELETE"})
     */
    public function deleteLessonFromClass(LessonRepository $lessonRepo, ClassroomRepository $classroomRepo, Request $request): RedirectResponse
    {
        // find lesson
        $lesson_id = $request->attributes->get('id');
        $lesson = $lessonRepo->findOneById($lesson_id);
        // find classroom
        $classroom_id = $request->query->get('classroom_id');
        $classroom = $classroomRepo->findOneById($classroom_id);

        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$lesson_id,
            $request->get('_token')
        )) {
            $classroom->removeLesson($lesson);
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Module supprimé avec succès.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom_id,
        ]);
    }
}
