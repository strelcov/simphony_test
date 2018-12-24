<?php

namespace AppBundle\Controller;

use AppBundle\Action\AddBook;
use AppBundle\Action\DeleteBook;
use AppBundle\Action\DeleteBookFile;
use AppBundle\Action\DeleteBookScreen;
use AppBundle\Action\UpdateBook;
use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{

    /**
     * @var BookRepository
     */
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Lists all book entities.
     *
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function indexAction()
    {
        $books = $this->bookRepository->findAllWithReadSort();

        return $this->render('book/index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/book/new", name="book_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AddBook $addBookAction */
            $addBookAction = $this->container->get(AddBook::class);
            $addBookAction->execute($book, $form, true);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/book/{id}/edit", name="book_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UpdateBook $updateBookAction */
            $updateBookAction = $this->container->get(UpdateBook::class);
            $updateBookAction->execute($book, $editForm, true);

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
     * @Route("/book/{id}/delete", name="book_delete", methods={"GET"})
     */
    public function deleteAction(Book $book)
    {
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        /** @var DeleteBook $deleteBookAction */
        $deleteBookAction = $this->container->get(DeleteBook::class);
        $deleteBookAction->execute($book);

        return $this->redirectToRoute('homepage');
    }
    /**
     * Download a book.
     *
     * @Route("/book/{id}/download", name="book_download", methods={"GET"})
     */
    public function downloadAction(Book $book)
    {
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        if (empty($book->getFilePath())) {
            throw new NotFoundHttpException('Книга не была загружена');
        }
        if (!$book->getAllowDownload()) {
            throw new NotFoundHttpException('Скачивание запрещено');
        }
        $path = $this->getParameter('books_directory') . '/' . $book->getFilePath();

        return $this->file($path);
    }

    /**
     * Deletes a book screen.
     *
     * @Route("/book/{id}/delete-screen", name="book_delete_screen", methods={"POST"})
     */
    public function deleteScreenAction(Book $book)
    {
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        if (empty($book->getScreen())) {
            throw new NotFoundHttpException('Обложка не найдена');
        }
        /**
         * @var DeleteBookScreen $deleteScreenAction
         */
        $deleteScreenAction = $this->container->get(DeleteBookScreen::class);
        $deleteScreenAction->execute($book);

        return new Response();
    }

    /**
     * Deletes a book file.
     *
     * @Route("/book/{id}/delete-file", name="book_delete_file", methods={"POST"})
     */
    public function deleteFileAction(Book $book)
    {
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        if (empty($book->getFilePath())) {
            throw new NotFoundHttpException('Файл книги не найден');
        }
        /**
         * @var DeleteBookScreen $deleteScreenAction
         */
        $deleteFileAction = $this->container->get(DeleteBookFile::class);
        $deleteFileAction->execute($book);

        return new Response();
    }
}
