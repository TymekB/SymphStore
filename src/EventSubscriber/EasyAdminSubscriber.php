<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 07.07.18
 * Time: 10:56
 */

namespace App\EventSubscriber;


use App\Entity\Product;
use EasySlugger\Slugger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var Slugger
     */
    private $slugger;

    /**
     * EasyAdminSubscriber constructor.
     * @param Slugger $slugger
     */
    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }


    public static function getSubscribedEvents()
    {
        return ['easy_admin.pre_persist' => ['setSlugToProduct']];
    }

    public function setSlugToProduct(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if(!($entity instanceof Product)) {
            return;
        }

        $slug = $this->slugger->slugify($entity->getName());
        $entity->setSlug($slug);

        $event['entity'] = $entity;
    }
}