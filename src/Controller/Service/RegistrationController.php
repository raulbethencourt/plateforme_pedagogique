<?php

namespace App\Controller\Service;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
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

/**
 * Class RegistrationController
 * This class manage the different users registrations.
 */
class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $find;
    private $request;

    public function __construct(
        EmailVerifier $emailVerifier,
        FindEntity $find,
        RequestStack $requestStack
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->find = $find;
        $this->request = $requestStack->getCurrentRequest();
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
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('raoulbetilla@gmail.com', 'Plataform Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Merci de confirmer votre email.')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('app_login');
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
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            // dd($request, $this->getUser());
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
            return $this->render('registration/user_verify.html.twig');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }
    }

    /**
     * @Route("/confirmation", name="confirm_mail")
     */
    public function renderConfirmation()
    {
        return $this->render('registration/confirm.html.twig');
    }
}
