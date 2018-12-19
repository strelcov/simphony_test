<?php

namespace AppBundle\Controller;

use AppBundle\Action\AddBook;
use AppBundle\Action\UpdateBook;
use AppBundle\Entity\Book;
use AppBundle\Form\BookApiType;
use AppBundle\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{

    /**
     * @var BookRepository
     */
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    private function serialize($data)
    {
        return $this->container->get('jms_serializer')->serialize($data, 'json');
    }

    private function validateAuth($apikey)
    {
        if (empty($apikey)) {
            return new JsonResponse([
                'error' => 'api key is empty'
            ], 401);
        }
        if ($apikey != $this->getParameter('apikey')) {
            return new JsonResponse([
                'error' => 'api is incorrect'
            ], 401);
        }

        return true;
    }

    /**
     * Lists all book entities.
     *
     * @Route("/api/v1/books", name="api_books")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $apikey = $request->get('apikey', null);
        $validateApiKey = $this->validateAuth($apikey);
        if ($validateApiKey !== true) {
            return $validateApiKey;
        }
        $books = $this->bookRepository->findAllWithReadSort();

        return new JsonResponse([
            'books' => $this->serialize($books),
        ], 200);
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/api/v1/books/add", name="api_books_add")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $apikey = $request->get('apikey', null);
        $validateApiKey = $this->validateAuth($apikey);
        if ($validateApiKey !== true) {
            return $validateApiKey;
        }
        $book = new Book();
        $form = $this->createForm(BookApiType::class, $book);
        //$form->submit($request->request->all());    // $_POST
        $form->submit($request->query->all());      // $_GET
        if (!$form->isValid()) {
            return new JsonResponse($this->serialize($form->getErrors()), 400);
        }
        /** @var AddBook $addBookAction */
        $addBookAction = $this->container->get(AddBook::class);
        $addBookAction->execute($book, $form, false);

        return new JsonResponse([
            'book' => $this->serialize($book),
        ], 200);
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/api/v1/books/{id}/edit", name="api_book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $apikey = $request->get('apikey', null);
        $validateApiKey = $this->validateAuth($apikey);
        if ($validateApiKey !== true) {
            return $validateApiKey;
        }
        $editForm = $this->createForm(BookApiType::class, $book);
        //$editForm->submit($request->request->all());    // $_POST
        $editForm->submit($request->query->all());      // $_GET
        if (!$editForm->isValid()) {
            return new JsonResponse($this->serialize($editForm->getErrors()), 400);
        }
        /** @var UpdateBook $updateBookAction */
        $updateBookAction = $this->container->get(UpdateBook::class);
        $updateBookAction->execute($book, $editForm, false);

        return new JsonResponse([
            'book' => $this->serialize($book),
        ], 200);
    }

}
