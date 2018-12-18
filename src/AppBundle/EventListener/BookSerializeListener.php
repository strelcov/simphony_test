<?php
namespace AppBundle\EventListener;


use AppBundle\Entity\Book;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

/**
 * Add data before serialization
 *
 */
class BookSerializeListener implements EventSubscriberInterface
{

    private $domain;

    public function __construct($domain = '%domain%')
    {
        $this->domain = $domain;
    }

    static public function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Book::class,
                'method' => 'onPreSerialize'
            ],
        ];
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $book = $event->getObject();
        if ($book instanceof Book) {
            $screen = $book->getScreen();
            if (!empty($screen)) {
                $book->setScreen($this->domain . '/' . $screen);
            }
            $file = $book->getFilePath();
            if (!empty($file)) {
                $book->setFilePath($this->domain . '/' . $file);
            }
        }
    }
}