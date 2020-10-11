<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Invite;
use App\Form\InviteType;
use App\invitation\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassroomController
 * This class manage the classrooms
 * @Route("/classroom")
 * @package App\Controller
 */
class ClassroomController extends AbstractController
{
    /**
     * This methode shows the students and teacher that belongs to the classroom
     * and It allows us to invite new Teachers or students
     * @Route("/{id}", name="classroom_index")
     * @IsGranted ("ROLE_USER")
     * @param Classroom $classroom
     * @param Request $request
     * @param Invitation $invitation
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function index(Classroom $classroom, Request $request, Invitation $invitation): Response
    {
        $invite = new Invite(); // New teacher or student

        $form = $this->createForm(InviteType::class, $invite);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->invite($invite, $classroom->getId(), $classroom->getDiscipline());
            $this->addFlash('success', 'Votre invitation a bien été envoyée.');
            return $this->redirectToRoute('classroom_index', [
                'id' => $classroom->getId()
            ]);
        }

        return $this->render(
            'user/classroom/index.html.twig',
            [
                'form' => $form->createView(),
                'classroom' => $classroom,
                'students' => $classroom->getStudents(),
                'teachers' => $classroom->getTeachers(),
            ]
        );
    }
}
