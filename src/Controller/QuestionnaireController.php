<?php

namespace App\Controller;

use App\Entity\Questionnaire;
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
     * @param  Request  $request
     * @return Response
     */
    public function index(Questionnaire $questionnaire, Request $request): Response
    {
        return $this->render(
            'questionnaire/index.html.twig',
            [
                'questionnaire' => $questionnaire,
            ]
        );
    }

    /**
     * @Route("/play", name="questionnaire_play")
     */
    public function play(): Response
    {

    }
}
