<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuizController
 * @package App\Controller
 * @Route ("/questionnaire", name="questionnaire")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/questionnaire", name="questionnaire_juex")
     */
    public function play()
    {
        return $this->render(':Questionnaire:play.twig', [
            'controller_name' => 'QuizController',
        ]);
    }
}
