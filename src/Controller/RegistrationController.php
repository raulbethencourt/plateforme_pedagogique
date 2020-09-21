<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\TeacherRegistrationType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use function Symfony\Component\String\u;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     * @param  Request  $request
     * @param  UserPasswordEncoderInterface  $passwordEncoder
     * @param  GuardAuthenticatorHandler  $guardHandler
     * @param  LoginFormAuthenticator  $authenticator
     * @return Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

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
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
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
     * @Route("/usager_register", name="usager_register")
     * @param  Request  $request
     * @param  UserPasswordEncoderInterface  $passwordEncoder
     * @param  GuardAuthenticatorHandler  $guardHandler
     * @param  LoginFormAuthenticator  $authenticator
     * @return Response
     */
    public function usagerRegister(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response {
        $type = $request->query->get("type");

        switch ($type) {
            case 'teacher':
                $usager = new Teacher();
                $form = $this->createForm(TeacherRegistrationType::class, $usager);
                break;
            default:
                $usager = new Student();
                $form = $this->createForm(StudentRegistrationType::class, $usager);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $usager->setPassword(
                $passwordEncoder->encodePassword(
                    $usager,
                    $form->get('plainPassword')->getData()
                )
            );

            $classrooms = $form->getNormData();
            $classrooms = $classrooms->getClassrooms();

            foreach ($classrooms as $classroom) {
                switch ($type) {
                    case 'teacher':
//                        dd($classroom);
                        $usager->addClassroom($classroom);
                        break;
                    default:
                        $usager->addClassroom($classroom);
                }

            }

            $usager->setEntryDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usager);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $usager,
                (new TemplatedEmail())
                    ->from(new Address('raoulbetilla@gmail.com', 'Plataform Mail Bot'))
                    ->to($usager->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $usager,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        if ($type === 'teacher') {
            return $this->render(
                'registration/teacher_register.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
        }

        return $this->render(
            'registration/student_register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        switch (get_class($this->getUser())) {
            case Teacher::class:
                return $this->redirectToRoute('teacher_index');
                break;
            case Student::class:
                return $this->redirectToRoute('student_index');
                break;
            default:
                return $this->redirectToRoute('user_index');
        }
    }
}
