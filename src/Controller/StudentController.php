<?php

namespace App\Controller;

use App\Entity\Pass;
use App\Entity\Questionnaire;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student_index")
     * @IsGranted("ROLE_STUDENT")
     */
    public function index()
    {
        $student = $this->getUser();
        $classrooms = $student->getClassrooms();

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
     * @Route("/student/profile", name="student_profile")
     * @IsGranted("ROLE_STUDENT")
     * @return Response
     */
    public function profile()
    {
        $passes = $this->getDoctrine()->getRepository(Pass::class)->findBy(['student' => $this->getUser()]);

        $sum = array_reduce(
            $passes,
            function ($i, $play) {
                return $i += $play->getScore();
            }
        );

        $numberOfQuestions = array_reduce(
            $passes,
            function ($i, $play) {
                return $i += count($play->getQuestionnaire()->getQuestions());
            }
        );

        $diffs = Questionnaire::DIFFICULTIES;
        $playsPerDiff = [];
        $statsPerDiff = [];

        foreach ($diffs as $diff) {
            $playsPerDiff[$diff] = array_filter(
                $passes,
                function ($play) use ($diff) {
                    return $play->getQuestionnaire()->getDifficulty() == $diff;
                }
            );
            $totalScore = array_reduce(
                $playsPerDiff[$diff],
                function ($i, $play) {
                    return $i += $play->getQuestionnaire()->getTotalScore();
                }
            );
            $playerScore = array_reduce(
                $playsPerDiff[$diff],
                function ($i, $play) {
                    return $i += $play->getScore();
                }
            );
            if ($totalScore != null) {
                $statsPerDiff[$diff] = round(($playerScore / $totalScore) * 100, 2);
            } else {
                $statsPerDiff[$diff] = null;
            }
        }

        $sumMax = array_reduce(
            $plays,
            function ($i, $play) {
                return $i += $play->getQuestionnaire()->getTotalScore();
            }
        );

        $avg = (round($sum / $sumMax, 2) * 100)."%";

        //$percentAvg = $avg / count($plays);

        return $this->render(
            "security/profile.html.twig",
            [
                'plays' => $plays,
                'sum' => $sum,
                'avg' => $avg,
                'statsPerDiff' => $statsPerDiff,
                'spdjson' => json_encode(array_values($statsPerDiff)),
                'numberOfQuestions' => $numberOfQuestions,
            ]
        );
    }
}
