<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Form\ClassroomType;
use App\Form\EditUserType;
use App\Repository\ClassroomRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route ("/", name="user_index")
     * @param  ClassroomRepository  $repository
     * @return ResponseAlias
     */
    public function index(ClassroomRepository $repository): Response
    {
        $classrooms = $repository->findAll();
        $user = $this->getUser();

        return $this->render(
            'user/index.html.twig',
            [
                'classrooms' => $classrooms,
                'user' => $user,
            ]
        );
    }

    /**
     * @Route ("/classroom/create", name="user_classroom_create")
     * @param Request $request
     * @param Classroom|null $classroom
     * @return RedirectResponse|ResponseAlias
     */
    public function createClassroom(Request $request, Classroom $classroom = null, SchoolRepository $repository)
    {
        // Check if the classroom already exist
        if (!$classroom) {
            $classroom = new Classroom();
        }
        
        $school = $repository->findOneBy(['id' => '3']);
        $classroom->setSchool($school);

        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe créée avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/classroom/create.html.twig',
            [
                'classrooms' => $classroom,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/classroom/{id}/edit", name="user_classroom_edit", methods={"GET","POST"})
     * @param  Classroom  $classroom
     * @param  Request  $request
     * @return RedirectResponse|ResponseAlias
     */
    public function editClassroom(Classroom $classroom, Request $request)
    {
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Classe modifiée avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/classroom/edit.html.twig',
            [
                'classroom' => $classroom,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route ("/classroom/{id}/delete", name="user_classroom_delete", methods={"DELETE"})
     * @param  Classroom  $classroom
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function delete(Classroom $classroom, Request $request): RedirectResponse
    {
        // Check the token
        if ($this->isCsrfTokenValid(
            'delete'.$classroom->getId(),
            $request->get('_token')
        )) {
            $this->em->remove($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Classe supprimée avec succès.');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route ("/profile", name="user_profile")
     */
    public function userProfile()
    {
        return $this->render(
            'user/profile.html.twig',
            [
                'user' => $this->getUser(),
            ]
        );
    }



    /**
     * @Route ("/profile/edit", name="edit_user")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil édité avec succès.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'user/edit-profile.html.twig',
            [
                'editForm' => $form->createView(),
            ]
        );
    }
}
