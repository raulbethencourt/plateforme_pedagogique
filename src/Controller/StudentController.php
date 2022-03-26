<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\EditStudentType;
use App\Service\BreadCrumbsService as BreadCrumbs;
use App\Service\FindEntity;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    private $breadCrumbs;
    private $find;
    private $doctrine;

    public function __construct(
        FindEntity $find,
        BreadCrumbs $breadCrumbs,
        ManagerRegistry $doctrine
    ) {
        $this->find = $find;
        $this->breadCrumbs = $breadCrumbs;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", name="student_show")
     */
    public function show(PaginatorInterface $paginator, Request $request): Response
    {
        $student = $this->getUser();
        $classrooms = $student->getClassrooms();
        $lessons = [];

        if (count($classrooms) > 0) {
            foreach ($classrooms as $classroom) {
                $lessons[] = $paginator->paginate($classroom->getLessons(), $request->query->getInt('page', 1), 10);
            }
        }

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'lessons' => $lessons,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * @Route("/", name="student_classrooms")
     */
    public function showClassrooms(): Response
    {
        $student = $this->getUser();
        $classrooms = $student->getClassrooms();

        return $this->render('student/classrooms.html.twig', [
            'student' => $student,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * @Route("/profile", name="student_profile")
     */
    public function profile(): Response
    {
        $this->breadCrumbs->bcProfile(false, false);

        // Get each time that the student has passed q questionnaire
        $passes = $this->find->findPasses($this->getUser());

        $sum = array_reduce(
            $passes,
            function ($i, $pass) {
                return $i += $pass->getPoints();
            }
        );

        $numberOfQuestions = array_reduce(
            $passes,
            function ($i, $pass) {
                return $i += count($pass->getQuestionnaire()->getQuestions());
            }
        );

        $difficulties = Questionnaire::DIFFICULTIES;
        $playsPerDiff = [];
        $statsPerDiff = [];

        foreach ($difficulties as $difficulty) {
            $playsPerDiff[$difficulty] = array_filter(
                $passes,
                function ($pass) use ($difficulty) {
                    return $pass->getQuestionnaire()->getDifficulty() == $difficulty;
                }
            );
            $totalScore = array_reduce(
                $playsPerDiff[$difficulty],
                function ($i, $play) {
                    return $i += $play->getQuestionnaire()->getTotalScore();
                }
            );
            $playerScore = array_reduce(
                $playsPerDiff[$difficulty],
                function ($i, $play) {
                    return $i += $play->getPoints();
                }
            );
            if (null != $totalScore) {
                $statsPerDiff[$difficulty] = round(($playerScore / $totalScore) * 100, 2);
            } else {
                $statsPerDiff[$difficulty] = null;
            }
        }

        $sumMax = array_reduce(
            $passes,
            function ($i, $pass) {
                return $i += $pass->getQuestionnaire()->getTotalScore();
            }
        );

        if ($sumMax) {
            $average = (round($sum / $sumMax, 2) * 100).'%';
        } else {
            $average = 0;
        }

        return $this->render(
            'student/profile.html.twig',
            [
                'student' => $this->getUser(),
                'passes' => $passes,
                'sum' => $sum,
                'average' => $average,
                'statsPerDiff' => $statsPerDiff,
                'spdjson' => json_encode(array_values($statsPerDiff)),
                'numberOfQuestions' => $numberOfQuestions,
                'avatar' => $this->getUser()->getAvatar(),
            ]
        );
    }

    /**
     * @Route("/profile/edit", name="student_edit_profile")
     */
    public function editProfile(Request $request): Response
    {
        if ($request->query->get('list_profile_edit')) {
            $this->breadCrumbs->bcListUsers('students', $request->query->get('list_profile_edit'));
        } else {
            $this->breadCrumbs->bcProfile(true);
        }

        $student_name = $request->query->get('username');

        if (isset($student_name)) {
            $student = $this->find->findStudentByUsername($student_name);
        } else {
            $student = $this->getUser();
        }

        $form = $this->createForm(EditStudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($student);
            $entityManager->flush();
            $this->addFlash('success', 'Profil édité avec succès.');

            if (isset($student_name)) {
                return $this->redirectToRoute('user_list', [
                    'type' => 'students',
                ]);
            }

            return $this->redirectToRoute('student_profile');
        }

        return $this->render(
            'student/edit-profile.html.twig',
            [
                'editForm' => $form->createView(),
                'student' => $this->getUser(),
            ]
        );
    }
}
