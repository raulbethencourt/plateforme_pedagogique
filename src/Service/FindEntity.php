<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class FindEntity
{
    private $classroomRepo;

    private $lessonRepo;

    private $userRepo;

    private $request;

    public function __construct(ClassroomRepository $classroomRepo, LessonRepository $lessonRepo, RequestStack $query, UserRepository $userRepo)
    {
        $this->classroomRepo = $classroomRepo;
        $this->lessonRepo = $lessonRepo;
        $this->request = $query;
        $this->userRepo = $userRepo;
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
}
