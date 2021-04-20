<?php

namespace App\Controller\Service;

use App\Entity\User;
use App\Entity\Invite;
use App\Entity\Student;
use App\Entity\Classroom;
use App\Service\FindEntity;
use Symfony\Component\Form\Form;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Invitation
 * This class manage email invitation for new users.
 */
class InvitationsController extends AbstractController
{
    private $mailer;

    private $em;

    private $request;

    private $find;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em, RequestStack $requestStack, FindEntity $find)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->find = $find;
    }

    /**
     * with this function I invite different users.
     */
    public function invitation(Form $form, Invite $invite, Classroom $classroom = null): RedirectResponse
    {
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if user is in the data base already
            $userAlready = $this->find->findUserAlready($invite->getName(), $invite->getSurname());

            if (isset($userAlready)) {
                $this->invite($invite, $classroom, $userAlready);
            } else {
                $this->invite($invite, $classroom);
            }

            $this->addFlash('success', 'Votre invitation a bien été envoyée.');
        }

        if (null !== $classroom) {
            return $this->redirectToRoute('classroom_index', [
                'id' => $classroom->getId(),
            ]);
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * send invitation to user.
     */
    public function invite(Invite $data, ?Classroom $classroom, User $user = null): void
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

    /**
     * this function prepare the email.
     */
    public function email(string $template, Invite $data, ?Classroom $classroom)
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
