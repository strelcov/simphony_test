<?php
namespace AppBundle\Helper;

use AppBundle\Entity\Book;
use AppBundle\Service\FileUploader;
use Symfony\Component\Form\Form;

class BookFileSaver
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param Book $book
     * @param Form $form
     */
    public function saveFiles(Book $book, Form $form)
    {
        if (!empty($form['screen']->getData())) {
            $screenName = $this->fileUploader->upload(
                $form['screen']->getData(),
                $book->getId(),
                $book->getCreatedAt()->format('Y-m')
            );
            $oldScreenName = $book->getScreen();
            if (!empty($oldScreenName)) {
                $this->fileUploader->deleteFile($oldScreenName);
            }
            $book->setScreen($screenName);
        }
        if (!empty($form['filePath']->getData())) {
            $fileName = $this->fileUploader->upload(
                $form['filePath']->getData(),
                $book->getId(),
                $book->getCreatedAt()->format('Y-m')
            );
            $oldFileName = $book->getFilePath();
            if (!empty($oldFileName)) {
                $this->fileUploader->deleteFile($oldFileName);
            }
            $book->setFilePath($fileName);
        }
    }
}