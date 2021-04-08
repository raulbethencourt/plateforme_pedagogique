<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class FindEntity
{
    private $classroomRepo;

    private $lessonRepo;

    private $userRepo;

    private $request;

    private $notificationRepo;

    public function __construct(ClassroomRepository $classroomRepo, LessonRepository $lessonRepo, RequestStack $query, UserRepository $userRepo, NotificationRepository $notificationRepo)
    {
        $this->classroomRepo = $classroomRepo;
        $this->lessonRepo = $lessonRepo;
        $this->request = $query;
        $this->userRepo = $userRepo;
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Find a classroom.
     */
    public function findClassroom(): Classroom
    {
        if (null !== $this->request->getCurrentRequest()->query->get('classroom_id')) {
            $classroom_id = $this->request->getCurrentRequest()->query->get('classroom_id');
        } else {
            $classroom_id = $this->request->getCurrentRequest()->attributes->get('id');
        }

        return $this->classroomRepo->findOneById($classroom_id);
    }

    /**
     * Find a lesson.
     */
    public function findLesson(): Lesson
    {
        if (null !== $this->request->getCurrentRequest()->query->get('lesson_id')) {
            $classroom_id = $this->request->getCurrentRequest()->query->get('lesson_id');
        } else {
            $lesson_id = $this->request->getCurrentRequest()->attributes->get('id');
        }

        return $this->lessonRepo->findOneById($lesson_id);
    }

    /**
     * find all lessons.
     */
    public function findAllLessons(): array
    {
        return $this->lessonRepo->findAll();
    }

    /**
     * find all classrooms.
     */
    public function findAllClassrooms(): array
    {
        return $this->classroomRepo->findAll();
    }

    /**
     * find users by role.
     */
    public function findUsersByRole(string $role): array
    {
        return $this->userRepo->findByRole($role);
    }

    /**
     * find user by id.
     */
    public function findUser(): User
    {
        $user_id = $this->request->getCurrentRequest()->attributes->get('id');

        return $this->userRepo->findOneById($user_id);
    }

    /**
     * find user that its already in the db.
     */
    public function findUserAlready(string $name, string $surname): ?User
    {
        return $this->userRepo->findOneBy([
            'name' => $name,
            'surname' => $surname,
        ]);
    }

    /**
     * find notification in classroom.
     */
    public function findNotification(Classroom $classroom): Notification
    {
        return $this->notificationRepo->findOneBy(['classroom' => $classroom]);
    }
}
