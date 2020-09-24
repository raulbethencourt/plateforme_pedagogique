<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Invite;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\InviteType;
use App\invitation\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClassroomController extends AbstractController
{
    /**
     * @Route("/classroom/{id}", name="classroom_index")
     * @param  Classroom  $classroom
     * @param  Request  $request
     * @param  Invitation  $invitation
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function index(Classroom $classroom, Request $request, Invitation $invitation): Response
    {
        $invite = new Invite();
        $form = $this->createForm(InviteType::class, $invite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->invite($invite, $classroom->getId());
            $this->addFlash('success', 'Votre invitation a bien été envoyé');
            return $this->redirectToRoute('classroom_index', [
                'id' => $classroom->getId()
            ]);
        }

        return $this->render(
            'classroom/index.html.twig',
            [
                'form' => $form->createView(),
                'classroom' => $classroom,
                'students' => $classroom->getStudents(),
                'teachers' => $classroom->getTeachers(),
            ]
        );
    }
}
