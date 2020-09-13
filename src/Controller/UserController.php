<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route ("/", name="user_index")
     * @return ResponseAlias
     */
    public function index(): ResponseAlias
    {
        $questionnaires = $this->repository->findAll();

        return $this->render('user/index.html.twig', compact('questionnaires'));
    }
}
