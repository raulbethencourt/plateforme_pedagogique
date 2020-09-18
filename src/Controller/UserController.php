<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Teacher;
use App\Form\ClassroomType;
use App\Form\UserTeacherRegistrationType;
use App\Repository\ClassroomRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route ("/", name="user_index")
     * @param ClassroomRepository $repository
     * @return ResponseAlias
     */
    public function index(ClassroomRepository $repository): Response
    {
        $classrooms = $repository->findAll();
        $user = $this->getUser()->getUsername();

        return $this->render('user/index.html.twig', [
            'classrooms' => $classrooms,
            'user' => $user
        ]);
    }

    /**
     * @Route ("/classroom/create", name="user_classroom_create")
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function createClassroom(Request $request)
    {
        $classroom = new Classroom();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Bien crée avec succès');

            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/classroom/create.html.twig', [
            'classrooms' => $classroom,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/classroom/{id}", name="user_classroom_edit", methods={"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|ResponseAlias
     */
    public function editClassroom(Classroom $classroom,Request $request)
    {
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');

            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/classroom/edit.html.twig', [
            'classroom' => $classroom,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/classroom/{id}", name="user_classroom_delete", methods={"DELETE"})
     * @param Classroom $classroom
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Classroom $classroom, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $classroom->getId(),
            $request->get('_token'))) {
            $this->em->remove($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/register", name="teacher_register")
     * @param Classroom $classroom
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return ResponseAlias
     */
        public function teacherRegister(Request $request, UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        dd($request);
        $teacher = new Teacher();
        $form = $this->createForm(UserTeacherRegistrationType::class, $teacher);
        $form->handleRequest($request);

        $teacher->setEntryDate(new \DateTime());
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $teacher->setPassword(
                $passwordEncoder->encodePassword(
                    $teacher,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($teacher);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $teacher,
                (new TemplatedEmail())
                    ->from(new Address('raoulbetilla@gmail.com', 'Plataform Mail Bot'))
                    ->to($teacher->getEmail())
                    ->subject('vous ete bien ajoute comment formateur')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $teacher,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('user/teacher/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
