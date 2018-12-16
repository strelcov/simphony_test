<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findAll();

        return $this->render('book/index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/book/new", name="book_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, FileUploader $fileUploader)
    {
        $book = new Book();
        $form = $this->createForm('AppBundle\Form\BookType', $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);

            $this->saveFiles($book, $fileUploader);

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
        ));
    }

    private function saveFiles(Book $book, FileUploader $fileUploader)
    {
        if (!empty($book->getScreen())) {
            $screenName = $fileUploader->upload(
                $book->getScreen(),
                $book->getId(),
                $book->getCreatedAt()->format('Y-m')
            );
            $book->setScreen($screenName);
        } else {
            //TODO: не получилось сделать по умолчанию пустую строку, в бд хочет записаться null
            $book->setScreen('');
        }
        if (!empty($book->getFilePath())) {
            $fileName = $fileUploader->upload(
                $book->getFilePath(),
                $book->getId(),
                $book->getCreatedAt()->format('Y-m')
            );
            $book->setFilePath($fileName);
        } else {
            //TODO: не получилось сделать по умолчанию пустую строку, в бд хочет записаться null
            $book->setFilePath('');
        }
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/book/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $editForm = $this->createForm('AppBundle\Form\BookType', $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_edit', array('id' => $book->getId()));
        }

        return $this->render('book/edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/book/{id}/delete", name="book_delete")
     * @Method("GET")
     */
    public function deleteAction(Book $book)
    {
        if(!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
    /**
     * Download a book.
     *
     * @Route("/book/{id}/download", name="book_download")
     * @Method("GET")
     */
    public function downloadAction(Book $book)
    {
        if(!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        if(empty($book->getFilePath())) {
            throw new NotFoundHttpException('Книга не была загружена');
        }
        if(!$book->getAllowDownload()) {
            throw new NotFoundHttpException('Скачивание запрещено');
        }
        $path = $this->getParameter('books_directory') . '/' . $book->getFilePath();

        return $this->file($path);
    }
}
