<?php

namespace App\Controller\Service;

use App\Entity\Classroom;
use App\Service\FindEntity;
use App\Entity\Notification;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationsController extends AbstractController
{
    private $find;

    private $request;

    private $em;

    public function __construct(FindEntity $find, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->find = $find;
        $this->request = $requestStack;
        $this->em = $em;
    }

    /**
     * with this function the admin can send a notification to classroom students.
     */
    public function notify(Notification $notification, Classroom $classroom, Form  $formNotify): void
    {
        $notification_old = $this->find->findNotification($classroom);
        $formNotify->handleRequest($this->request->getCurrentRequest());

        if ($formNotify->isSubmitted() && $formNotify->isValid()) {
            if ($notification_old) {
                $classroom->removeNotification($notification_old);
            }
            $this->em->persist($notification);
            $this->em->flush();
            $this->addFlash('success', 'Notification ajouté avec succès.');
        }
    }
}
