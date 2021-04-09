<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Notification;
use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use App\Repository\NotificationRepository;
use App\Repository\PassRepository;
use App\Repository\QuestionnaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class FindEntity
{
    private $classroomRepo;

    private $lessonRepo;

    private $userRepo;

    private $request;

    private $notificationRepo;

    private $questionnaireRepo;

    private $questionRepo;

    private $passRepo;

    public function __construct(ClassroomRepository $classroomRepo, LessonRepository $lessonRepo, RequestStack $requestStack, UserRepository $userRepo, NotificationRepository $notificationRepo, QuestionnaireRepository $questionnaireRepo, QuestionRepository $questionRepo, PassRepository $passRepo)
    {
        $this->classroomRepo = $classroomRepo;
        $this->lessonRepo = $lessonRepo;
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepo = $userRepo;
        $this->notificationRepo = $notificationRepo;
        $this->questionnaireRepo = $questionnaireRepo;
        $this->questionRepo = $questionRepo;
        $this->passRepo = $passRepo;
    }

    /**
     * Find a classroom.
     */
    public function findClassroom(): ?Classroom
    {
        if (null !== $this->request->query->get('classroom_id')) {
            $classroom_id = $this->request->query->get('classroom_id');
        } else {
            $classroom_id = $this->request->attributes->get('id');
        }

        return $this->classroomRepo->findOneById($classroom_id);
    }

    /**
     * Find a lesson.
     */
    public function findLesson(): ?Lesson
    {
        if (null !== $this->request->query->get('lesson_id')) {
            $lesson_id = $this->request->query->get('lesson_id');
        } else {
            $lesson_id = $this->request->attributes->get('id');
        }
        
        return $this->lessonRepo->findOneById($lesson_id);
    }

    /**
     * Find a Questionnaire.
     */
    public function findQuestionnaire(): ?Questionnaire
    {
        if (null !== $this->request->query->get('questionnaire_id')) {
            $questionnaire_id = $this->request->query->get('questionnaire_id');
        } else {
            $questionnaire_id = $this->request->attributes->get('id');
        }

        return $this->questionnaireRepo->findOneById($questionnaire_id);
    }

    /**
     * find a Question.
     */
    public function findQuestion(): ?Question
    {
        if (null !== $this->request->query->get('question_id')) {
            $question_id = $this->request->query->get('question_id');
        } else {
            $question_id = $this->request->attributes->get('id');
        }

        return $this->questionRepo->findOneById($question_id);
    }

    /**
     * find pass
     */
    public function findPass(User $user, Questionnaire $questionnaire): ?Pass
    {
        return $this->passRepo->findOneBy(['student' => $user, 'questionnaire' => $questionnaire]);
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
     * find all questionnaires.
     */
    public function findAllQuestionnaires(): array
    {
        return $this->questionnaireRepo->findAll();
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
        $user_id = $this->request->attributes->get('id');

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
