<?php

namespace App\EventSubscriber;

use App\Repository\EventsRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $eventsRepository;
    private $router;

    public function __construct(
        EventsRepository $eventsRepository,
        UrlGeneratorInterface $router
    ) {
        $this->eventsRepository = $eventsRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $events = $this->eventsRepository
            ->createQueryBuilder('events')
            ->where('events.beginAt BETWEEN :start and :end OR events.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();

        foreach ($events as $event) {
            // this create the events with your data (here event data) to fill calendar
            $eventAction = new Event(
                $event->getTitle(),
                $event->getBeginAt(),
                $event->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $eventAction->setOptions([
                'textColor' => '#080808',
                'backgroundColor' => '#75aaae',
                'borderColor' => '#75aaae',
                'classNames' => 'pl-2'
            ]);

            $event = $event->getId_events();
            $eventAction->addOption(
                'url',
                $this->router->generate('events_show', [
                    'id_events' => $event,
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($eventAction);
        }
    }
}
