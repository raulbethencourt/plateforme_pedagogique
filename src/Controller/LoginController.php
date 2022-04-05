<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/", name="login")
     * @Route("/login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
    * @Route("/secure-area", name="home_page")
    */
    public function indexAction() : ?Response
    {
        $role = $this->getUser()->getRoles()[0];
        // I do a redirection using the user type to arrive a different parts of the application
        switch ($role) {
            case 'ROLE_TEACHER':
                return $this->redirect(
                    $this->generateUrl(
                        'teacher_show',
                        ['user' => $this->getUser()]
                    )
                );
            case 'ROLE_STUDENT':
                return $this->redirect(
                    $this->generateUrl(
                            'student_show',
                            ['user' => $this->getUser()]
                        )
                    );
            default:
                return $this->redirect($this->generateUrl('user_show'));
        }
        throw new \Exception(AccessDeniedException::class);
    }
}
