<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Repository\QuestionnaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @var QuestionnaireRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(QuestionnaireRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/student", name="student_index")
     */
    public function index()
    {
        $student = $this->getUser();
        return $this->render('student/index.html.twig', [
            'student' => $student,
            'questionnaires' => $this->getDoctrine()
                ->getRepository(Questionnaire::class)
                ->findAll(),
        ]);
    }


}
