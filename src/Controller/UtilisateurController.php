<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UtilisateurController
 * @package App\Controller
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route ("/", name="utilisateur_index")
     * @return ResponseAlias
     */
    public function index(): ResponseAlias
    {
        $questionnaires = $this->repository->findAll();

        return $this->render('utilisateur/index.html.twig', compact('questionnaires'));
    }
}
