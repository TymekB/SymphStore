<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.08.18
 * Time: 17:53
 */

namespace App\EventSubscriber;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductEditUploadImageTriggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [EasyAdminEvents::PRE_UPDATE => ['upload']];
    }

    public function upload(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if(!($entity instanceof Product)) {
            return;
        }

        if(!$this->em->getUnitOfWork()->isScheduledForUpdate($entity)) {
            $this->em->getUnitOfWork()->scheduleForUpdate($entity);
        }
    }
}