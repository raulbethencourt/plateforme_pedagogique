<?php

namespace App\Service;

use App\Entity\Avatar;
use App\Entity\Question;
use App\Entity\Questionnaire;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    private $helper;

    private $cache;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cache = $cacheManager;
        $this->helper = $uploaderHelper;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preRemove',
            'preUpdate',
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntityManager();
        if ($entity instanceof Avatar
            || $entity instanceof Questionnaire
            || $entity instanceof Question) {
            $this->cache->remove($this->helper->asset($entity, 'imageFile'));
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Avatar
            || $entity instanceof Questionnaire
            || $entity instanceof Question) {
            if ($entity->getImageFile() instanceof UploadedFile) {
                $this->cache->remove($this->helper->asset($entity, 'imageFile'));
            }
        }
    }
}
