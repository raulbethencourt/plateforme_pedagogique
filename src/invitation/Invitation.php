<?php


namespace App\invitation;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use Symfony\Component\Mime\Address;
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

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $data
     * @param $classroom
     * @param Teacher|Student $user
     * @throws TransportExceptionInterface
     */
    public function invite($data, Classroom $classroom, $user = null): void
    {
        if (isset($user)) {
            
            $user = $user->addClassrooms($classroom);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->email('emails/old_invitation.html.twig', $data, $classroom);
        } else {
            // If the user is not in the data base
            // an email is sent to the new user
            $this->email('emails/new_invitation.html.twig', $data, $classroom);
        }
    }

    public function email($template, $data, Classroom $classroom)
    {
        $email = (new TemplatedEmail())
                ->from(Address::fromString('Carpa <carpa@exemple.com>'))
                ->to($data->getEmail())
                ->subject('Invitation à Carpa')
                ->htmlTemplate($template)
                ->context(
                    [
                        'data' => [
                            'type' => $data->getType(),
                            'name' => $data->getName(),
                            'classroom' => $classroom->getId(),
                            'discipline' => $classroom->getDiscipline(),
                        ],
                    ]
                );
            return $this->mailer->send($email);
    }
}