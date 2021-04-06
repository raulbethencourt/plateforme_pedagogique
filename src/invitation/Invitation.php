<?php


namespace App\invitation;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class Invitation
 * This class manage email invitation for nex users
 * @package App\invitation
 */
class Invitation extends AbstractController
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    /**
     * @param $data
     * @param $classroom
     * @param Teacher|Student $user
     * @throws TransportExceptionInterface
     */
    public function invite($data, Classroom $classroom = null, $user = null): void
    {
        if (isset($user)) {
            if ($user->getRoles()[0] === 'ROLE_STUDENT') {
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
            ->htmlTemplate($template);
        if (isset($classroom)) {
            $email->context(
                [
                    'data' => [
                        'type' => $data->getType(),
                        'name' => $data->getName(),
                        'classroom' => $classroom->getId(),
                        'discipline' => $classroom->getDiscipline(),
                    ]
                ]
            );
        } else {
            $email->context(
                [
                    'data' => ['name' => $data->getName()]
                ]
            );
        }
        return $this->mailer->send($email);
    }
}
