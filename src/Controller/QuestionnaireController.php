<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * @package App\Controller
 * @Route ("/questionnaire")
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @Route ("/{id}", name="questionnaire_index")
     * @param  QuestionRepository  $repository
     * @param  Request  $request
     * @return Response
     */
    public function index(QuestionRepository $repository, Request $request): Response
    {
        $questionnaire = $this->getDoctrine()
            ->getRepository(Questionnaire::class)
            ->findOneById($request->attributes->get('id'));

        $questionnaireTitle = $questionnaire->getTitle();
//        dd(is_string($questionnaire->getTitle()));


        $questions = $repository->findAll();

        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questions' => $questions,
                'title' => $questionnaireTitle,
            ]
        );
    }

    /**
     * @Route("/play", name="questionnaire_play")
     */
    public function play(): Response
    {
        return $this->render(
            'questionnaire/play.html.twig',
            [
                'controller_name' => 'QuestionnaireController',
            ]
        );
    }
}
