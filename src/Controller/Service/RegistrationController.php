<?php

namespace App\Controller\Service;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Service\FindEntity;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use App\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Class RegistrationController
 * This class manage the different users registrations
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    private $emailVerifier;

    private $find;

    private $request;

    public function __construct(EmailVerifier $emailVerifier, FindEntity $find, RequestStack $requestStack)
    {
        $this->emailVerifier = $emailVerifier;
        $this->find = $find;
        $this->request = $requestStack;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response {
        // I get User type property to change the registration way
        $type = $this->request->getCurrentRequest()->query->get("type");
        $classroom = $this->find->findClassroom();

        // In depends of type we creates different user
        switch ($type) {
            case 'teacher':
                $user = new Teacher();
                $user->setRoles(['ROLE_TEACHER']);
                break;
            case 'student':
                $user = new Student();
                $user->setRoles(['ROLE_STUDENT']);
                break;
            default:
                $user = new User();
                $user->setRoles(['ROLE_ADMIN']);
        }

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'method' => 'POST',
        ]);
        $form->handleRequest($this->request->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ($type === 'teacher' || $type === 'student') {
                $user->addClassrooms($classroom);
            }

            $user->setEntryDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('raoulbetilla@gmail.com', 'Plataform Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Merci de confirmer votre email.')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $this->request->getCurrentRequest(),
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($this->request->getCurrentRequest(), $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/user_verify.html.twig');
    }

    /**
     * @Route ("/confirmation", name="confirm_mail")
     */
    public function renderConfirmation()
    {
        return $this->render('registration/confirm.html.twig');
    }
}
