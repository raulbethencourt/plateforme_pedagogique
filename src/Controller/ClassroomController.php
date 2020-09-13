<?php

namespace App\Controller;


use App\Entity\Classroom;
use App\Form\ClassroomRegistrationType;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class ClassroomController
 * @package App\Controller
 * @Route ("/classroom")
 */
class ClassroomController extends AbstractController
{
    /**
     * @var ClassroomRepository
     */
    private $repository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        ClassroomRepository $repository,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    ) {
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @Route("/", name="classroom")
     */
    public function index()
    {
        return $this->render(
            'classroom/index.html.twig',
            [
                'controller_name' => 'ClassroomController',
            ]
        );
    }

    /**
     * @Route("/register", name="classroom_register")
     */
    public function classroomRegister(Request $request): Response
    {
        $classroom = new Classroom();
        $form = $this->createForm(ClassroomRegistrationType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $code = $form->get('access_code')->getData();

            $classroom = $this->repository->findOneBy(
                [
                    'name' => $name,
                ]
            );

            if (isset($classroom)) {
                $this->addFlash('error', 'this classroom already exist');
                return $this->redirectToRoute('classroom_register');
            }

            $classroom = new Classroom();

            $classroom->setName($name);
            $classroom->setAccessCode(
                $this->encoder->encodePassword($classroom, $code)
            );

            $this->em->persist($classroom);
            $this->em->flush();

            return $this->redirectToRoute(
                'classroom', [
                    'id' => $classroom->getId()
                ]
            );
        }

        return $this->render(
            'classroom/register.html.twig',
            [
                'classroomRegistrationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/login", name="classroom_login")
     * @param  AuthenticationUtils  $authenticationUtils
     */
    public function login(AuthenticationUtils $authenticationUtils): void
    {
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
    }
}
