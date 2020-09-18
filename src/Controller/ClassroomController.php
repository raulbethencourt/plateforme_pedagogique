<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassroomController extends AbstractController
{
    /**
     * @Route("/classroom/{id}", name="classroom_index")
     * @param Classroom $classroom
     * @return Response
     */
    public function index(Classroom $classroom): Response
    {
        return $this->render(
            'classroom/index.html.twig',
            [
                'classroom' => $classroom,
                'students' => $this->getDoctrine()->getRepository(Student::class)->findAll(),
                'teachers' => $this->getDoctrine()->getRepository(Teacher::class)->findAll(),
            ]
        );
    }
}
