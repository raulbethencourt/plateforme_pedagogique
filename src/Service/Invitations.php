<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Class Invitation
 * This class manage email invitation for new users.
 */
class Invitations
{
    private $mailer;

    private $em;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    /**
     * with this function I invite different users.
     *
     * @param \App\Repository\UserRepository $user
     * @param mixed                          $form
     */
    public function invitation(Classroom $classroom, Request $request, Invitation $invitation, UserRepository $userRepo, $form, Invite $invite): RedirectResponse
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if user is in the data base already
            $userAlready = $userRepo->findOneBy([
                'name' => $invite->getName(),
                'surname' => $invite->getSurname(),
            ]);

            if (isset($userAlready)) {
                $this->invite($invite, $classroom, $userAlready);
            } else {
                $this->invite($invite, $classroom);
            }

            $this->addFlash('success', 'Votre invitation a bien été envoyée.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom->getId(),
        ]);
    }

    /**
     * @param $data
     * @param $classroom
     * @param Teacher|Student $user
     *
     * @throws TransportExceptionInterface
     */
    public function invite($data, Classroom $classroom = null, $user = null): void
    {
        if (isset($user)) {
            if ('ROLE_STUDENT' === $user->getRoles()[0]) {
                $classroom->addStudent($user);
            } else {
                $classroom->addTeacher($user);
            }
            $this->em->persist($classroom);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur ajouté dans la classe avec succès.');
            $this->email('emails/old_invitation.html.twig', $data, $classroom);
        } else {
            // If the user is not in the data base
            // an email is sent to the new user
            $this->email('emails/new_invitation.html.twig', $data, $classroom);
        }
    }

    public function email($template, $data, ?Classroom $classroom)
    {
        $email = (new TemplatedEmail())
            ->from(Address::fromString('Carpa <carpa@exemple.com>'))
            ->to($data->getEmail())
            ->subject('Invitation à Carpa')
            ->htmlTemplate($template)
        ;
        if (isset($classroom)) {
            $email->context(
                [
                    'data' => [
                        'type' => $data->getType(),
                        'name' => $data->getName(),
                        'classroom' => $classroom->getId(),
                        'discipline' => $classroom->getDiscipline(),
                    ],
                ]
            );
        } else {
            $email->context(
                [
                    'data' => ['name' => $data->getName()],
                ]
            );
        }

        return $this->mailer->send($email);
    }
}
