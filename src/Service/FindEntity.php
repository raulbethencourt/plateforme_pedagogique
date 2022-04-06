<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Link;
use App\Entity\Notification;
use App\Entity\Pass;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use App\Repository\LinkRepository;
use App\Repository\NotificationRepository;
use App\Repository\PassRepository;
use App\Repository\QuestionnaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;

/*
* Helper class to get different entities
*/
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
    private $teacherRepo;
    private $studentRepo;
    private $linkRepo;

    public function __construct(
        ClassroomRepository $classroomRepo,
        LessonRepository $lessonRepo,
        RequestStack $requestStack,
        UserRepository $userRepo,
        NotificationRepository $notificationRepo,
        QuestionnaireRepository $questionnaireRepo,
        QuestionRepository $questionRepo,
        PassRepository $passRepo,
        TeacherRepository $teacherRepo,
        StudentRepository $studentRepo,
        LinkRepository $linkRepo
    ) {
        $this->classroomRepo = $classroomRepo;
        $this->lessonRepo = $lessonRepo;
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepo = $userRepo;
        $this->notificationRepo = $notificationRepo;
        $this->questionnaireRepo = $questionnaireRepo;
        $this->questionRepo = $questionRepo;
        $this->passRepo = $passRepo;
        $this->teacherRepo = $teacherRepo;
        $this->studentRepo = $studentRepo;
        $this->linkRepo = $linkRepo;
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
     * find lessons with search bar.
     */
    public function searchLesson(?string $title, ?string $level, ?string $creator, ?DateTime $date): array
    {
        return $this->lessonRepo->findBySearch($title, $level, $creator, $date);
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
     * find questionnaires with search bar.
     */
    public function searchQuestionnaire(?string $title, ?string $level, ?string $category, ?string $creator, ?DateTime $date): array
    {
        return $this->questionnaireRepo->findBySearch($title, $level, $category, $creator, $date);
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
     * Find a Link.
     */
    public function findLink(): ?Link
    {
        if (null !== $this->request->query->get('link_id')) {
            $link_id = $this->request->query->get('link_id');
        } else {
            $link_id = $this->request->attributes->get('id');
        }

        return $this->linkRepo->findOneById($link_id);
    }

    /**
     * find links with search bar.
     */
    public function searchLink(?string $name, ?string $category, ?string $creator): array
    {
        return $this->linkRepo->findBySearch($name, $category, $creator);
    }

    /**
     * find pass.
     */
    public function findPass(User $user, ?Questionnaire $questionnaire): ?Pass
    {
        return $this->passRepo->findOneBy(['student' => $user, 'questionnaire' => $questionnaire]);
    }

    /**
     * find user passes.
     */
    public function findPasses(User $user): array
    {
        return $this->passRepo->findBy(['student' => $user]);
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
     * find user with search bar.
     */
    public function searchUser(?string $name, ?string $surname, ?string $email, ?string $phone): array
    {
        return $this->userRepo->findBySearch($name, $surname, $email, $phone);
    }

    /**
     * find teacher by username.
     */
    public function findTeacherByUsername(string $user): Teacher
    {
        return $this->teacherRepo->findOneBy(['username' => $user]);
    }

    /**
     * find teacher by username.
     */
    public function findStudentByUsername(string $user): Student
    {
        return $this->studentRepo->findOneBy(['username' => $user]);
    }

    /**
     * find user by id.
     */
    public function findUser(): User
    {
        if (null !== $this->request->query->get('user_id')) {
            $user_id = $this->request->query->get('user_id');
        } else {
            $user_id = $this->request->attributes->get('id');
        }

        return $this->userRepo->findOneById($user_id);
    }

    /**
     * find user that its already in the db.
     */
    public function findUserAlready(string $email): ?User
    {
        return $this->userRepo->findOneBy(['email' => $email]);
    }

    /**
     * find notification in classroom.
     */
    public function findNotification(Classroom $classroom): ?Notification
    {
        return $this->notificationRepo->findOneBy(['classroom' => $classroom]);
    }
}
