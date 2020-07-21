<?php

namespace Snowdog\Academy\Controller\Admin;

use Snowdog\Academy\Core\Database;
use Snowdog\Academy\Model\Book;
use Snowdog\Academy\Model\BookManager;

class Books extends AdminAbstract
{
    private BookManager $bookManager;
    private ?Book $book;
    private int $days;

    public function __construct(BookManager $bookManager)
    {
        parent::__construct();
        $this->bookManager = $bookManager;
        $this->book = null;
    }

    public function index(): void
    {
        require __DIR__ . '/../../view/admin/books/list.phtml';
    }

    public function newBook(): void
    {
        require __DIR__ . '/../../view/admin/books/edit.phtml';
    }

    public function newBookPost(): void
    {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];

        if (empty($title) || empty($author) || empty($isbn)) {
            $_SESSION['flash'] = 'Missing data';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            return;
        }

        $this->bookManager->create($title, $author, $isbn);

        $_SESSION['flash'] = "Book $title by $author saved!";
        header('Location: /admin');
    }

    public function newBookPostCsv(): void
    {
        $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
        if(in_array($_FILES['file']['type'],$mimes)){
            $tmpName = $_FILES['file']['tmp_name'];
            $books = array_map('str_getcsv', file($tmpName));

            if (empty($books)) {
                $_SESSION['flash'] = 'CSV file empty';
            }

            foreach ($books as $book) {
                $title = $book[0];
                $author = $book[1];
                $isbn = $book[2];

                if (empty($title) || empty($author) || empty($isbn)) {
                    $_SESSION['flash'] .= "Missing data<br>";
                } else if (strlen($isbn) > 13) {
                    $_SESSION['flash'] .= "Book $title by $author NOT saved - ISBN too long<br>";
                } else {
                    $this->bookManager->create($title, $author, $isbn);
                    $_SESSION['flash'] .= "Book $title by $author saved!<br>";
                }
            }

        } else {
            $_SESSION['flash'] = 'Not a CSV file or no file selected';
        }

        header('Location: /admin');
    }

    public function edit(int $id): void
    {
        $book = $this->bookManager->getBookById($id);
        if ($book instanceof Book) {
            $this->book = $book;
            require __DIR__ . '/../../view/admin/books/edit.phtml';
        } else {
            header('HTTP/1.0 404 Not Found');
            require __DIR__ . '/../../view/errors/404.phtml';
        }
    }

    public function editPost(int $id): void
    {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];

        if (empty($title) || empty($author) || empty($isbn)) {
            $_SESSION['flash'] = 'Missing data';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            return;
        }

        $this->bookManager->update($id, $title, $author, $isbn);

        $_SESSION['flash'] = "Book $title by $author saved!";
        header('Location: /admin');
    }

    private function getBooks(): array
    {
        return $this->bookManager->getAllBooks();
    }

    private function getBorrowedDays(): array
    {
        return $this->bookManager->getAllBooks();
    }

    private function getBorrowedBooks(int $days): array
    {
        return $this->bookManager->getBorrowedBooks($days);
    }

    public function list(int $days = 1): void
    {
        if (!empty($_POST['days'])) {
            $this->days = $_POST['days'];
        } else {
            $this->days = $days;
        }
        require __DIR__ . '/../../view/admin/books/borrowed_list.phtml';
    }
}
