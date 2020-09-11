<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuestionnaireController
 * @package App\Controller
 * @Route ("/questionnaire", name="questionnaire")
 */
class QuestionnaireController extends AbstractController
{
    /**
     * @Route("/", name="questionnaire_juex")
     */
    public function play()
    {
        return $this->render(':Questionnaire:play.twig', [
            'controller_name' => 'QuizController',
        ]);
    }
}
