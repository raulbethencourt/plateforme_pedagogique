<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ToolboxController extends AbstractController
{
    /**
     * @Route("/toolbox", name="toolbox")
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')")
     */
    public function index(): Response
    {
        return $this->render('toolbox/index.html.twig');
    }
}
