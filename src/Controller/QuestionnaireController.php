<?php

namespace App\Controller;

use App\Repository\QuestionnaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * @package App\Controller
 * @Route ("/questionnaire", name="questionnaire")
 */
class QuestionnaireController extends AbstractController
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
     * @Route ("/{id}")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('questionnaire/index.html.twig');
    }

    /**
     * @Route("/play", name="questionnaire_play")
     */
    public function play(): Response
    {
        return $this->render('questionnaire/play.html.twig', [
            'controller_name' => 'QuestionnaireController',
        ]);
    }
}
