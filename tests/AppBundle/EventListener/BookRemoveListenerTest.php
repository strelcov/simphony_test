<?php

namespace Tests\AppBundle\EventListener;

use AppBundle\Action\DeleteBook;
use AppBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class BookRemoveListenerTest extends TestCase
{


    public function testCallListenerOnBookDelete()
    {
        $book = new Book();
        $book->setFilePath('file.txt');
        $book->setScreen('file.png');

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        /*$em->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(null);*/
        $deleteBookAction = new DeleteBook($em);
        $deleteBookAction->execute($book);

        //$this->assertNull($result);
    }
}