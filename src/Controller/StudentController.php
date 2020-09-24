<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student_index")
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
     * @return Response
     */
    public function profile()
    {
        return $this->render(
            'student/profile.html.twig',
            [
                'student' => $this->getUser(),
            ]
        );
    }
}
