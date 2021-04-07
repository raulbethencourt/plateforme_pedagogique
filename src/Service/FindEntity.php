<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class FindEntity
{
    private $classroomRepo;

    private $lessonRepo;

    private $request;

    public function __construct(ClassroomRepository $classroomRepo, LessonRepository $lessonRepo, RequestStack $query)
    {
        $this->classroomRepo = $classroomRepo;
        $this->lessonRepo = $lessonRepo;
        $this->request = $query;
    }

    /**
     * Find a classroom.
     */
    public function findClassroom(): Classroom
    {
        $classroom_id = $this->request->getCurrentRequest()->query->get('classroom_id');

        return $this->classroomRepo->findOneById($classroom_id);
    }

    /**
     * Find a lesson.
     */
    public function findLesson(): Lesson
    {
        $lesson_id = $this->request->getCurrentRequest()->attributes->get('id');

        return $this->lessonRepo->findOneById($lesson_id);
    }
}
