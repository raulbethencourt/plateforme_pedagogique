<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use App\Form\EditStudentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentController
 * This class manage student index and his profile
 * @Route("/student")
 * @package App\Controller
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student_index")
     */
    public function index(): Response
    {
        $student = $this->getUser();
        $classrooms = $student->getClassrooms();

        // We get all questionnaires that the student has access in this classroom
        foreach ($classrooms as $classroom) {
            $teachers = $classroom->getTeachers();
            foreach ($teachers as $teacher) {
                $questionnaires = $teacher->getQuestionnaires();
            }
        }

        return $this->render(
            'student/index.html.twig',
            [
                'student' => $student,
                'questionnaires' => $questionnaires,
            ]
        );
    }

    /**
     * This methode builds student profile
     * @Route("/profile", name="student_profile")
     */
    public function profile(): Response
    {
        // Get each time that the student has passed q questionnaire
        $passes = $this->getDoctrine()->getRepository(Pass::class)
            ->findBy(['student' => $this->getUser()]);

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
            if ($totalScore != null) {
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
            $average = (round($sum / $sumMax, 2) * 100)."%";
        } else {
            $average = 0;
        }

        return $this->render(
            "student/profile.html.twig",
            [
                'student' => $this->getUser(),
                'passes' => $passes,
                'sum' => $sum,
                'average' => $average,
                'statsPerDiff' => $statsPerDiff,
                'spdjson' => json_encode(array_values($statsPerDiff)),
                'numberOfQuestions' => $numberOfQuestions,
                'avatar' => $this->getUser()->getAvatar()
            ]
        );
    }

    /**
     * @Route ("/profile/edit", name="edit_student")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editProfile(Request $request)
    {
        $student = $this->getUser();

        $form = $this->createForm(EditStudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash('success', 'Profil édité avec succès.');

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
