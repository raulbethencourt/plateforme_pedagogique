<?php

namespace App\Service;

use App\Entity\Notification;

class Notifications
{
    private $find;

    public function __construct(FindEntity $find)
    {
        //TODO continuar
        $this->find = $find;
    }

    /**
     * with this function the admin can send a notification to classroom students.
     *
     * @param \App\Entity\Notification $notificationOld
     */
    public function notify(Form $formNotify, Notification $notification, NotificationRepository $repository): RedirectResponse
    {
        $classroom = $this->find->findClassroom();
        $notificationOld = $repository->findOneBy(['classroom' => $classroom]);
        $formNotify->handleRequest($request);

        if ($formNotify->isSubmitted() && $formNotify->isValid()) {
            if ($notificationOld) {
                $classroom->removeNotification($notificationOld);
            }
            $this->em->persist($notification);
            $this->em->flush();
            $this->addFlash('success', 'Notification ajouté avec succès.');
        }

        return $this->redirectToRoute('classroom_index', [
            'id' => $classroom->getId(),
        ]);
    }
}
