<?php

namespace App\Controller\Service;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\FindEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RegistrationController
 * This class manage the different users registrations.
 */
class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $find;
    private $request;
    private $mailer;

    public function __construct(
        VerifyEmailHelperInterface $verifyEmailHelper,
        FindEntity $find,
        RequestStack $requestStack,
        MailerInterface $mailer
    ) {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
        $this->mailer = $mailer;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     */
    public function register(
        UserPasswordHasherInterface $hasher,
        ManagerRegistry $doctrine
    ): Response {
        // I get User type property to change the registration way
        $type = $this->request->query->get('type');
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
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ('teacher' === $type || 'student' === $type) {
                $user->addClassroom($classroom);
            }

            $user->setEntryDate(new \DateTime());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation_route',
                $user->getId(),
                $user->getEmail()
            );
        
            $email = new TemplatedEmail();
            $email->from(new Address('fle@contact-promotion.org', 'contact-promotion'))
                ->to($user->getEmail())
                ->subject('Merci de confirmer votre email.')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
        
            $this->mailer->send($email);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify", name="registration_confirmation_route")
     */
    public function verifyUserEmail(
        Request $request, 
        TranslatorInterface $translator, 
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $translator->trans($e->getReason(), [], 'VerifyEmailBundle'));
            
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();
        
        $this->addFlash('success', 'Your e-mail address has been verified.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/confirmation", name="confirm_mail")
     */
    public function renderConfirmation()
    {
        return $this->render('registration/confirm.html.twig');
    }
}
