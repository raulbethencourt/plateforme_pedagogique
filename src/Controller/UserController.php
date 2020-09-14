<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     */
    public function index()
    {
       return  $this->render('user/index.html.twig');
       /* $questionnaires = $this->repository->findAll();

        return $this->render('user/index.html.twig', compact('questionnaires'));*/
    }
}
