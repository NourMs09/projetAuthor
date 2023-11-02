<?php

namespace App\Controller;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/book_list', name: 'book_list')]
    public function listBooks(): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }

   
    #[Route('/add', name: 'add')]
    public function addBook(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $book->setPublished(true);
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
    
            return $this->redirectToRoute('listpub');
        }
    
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listpub', name: 'listpub')]
    public function listPublishedBooks(): Response
    {
        $Book=$this->getDoctrine()
        ->getRepository(Book::class)
        ->findBy(['published'=>true]);

        return $this->render('book/list.html.twig',[
            'books'=>$Book,
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
 
   public function update(Request $request, $id,ManagerRegistry $mr,BookRepository $repo ):Response
{
    $book = $repo->find($id);
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);
    if ($form->isSubmitted() ) {
        $em=$mr->getManager();
        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('listpub');
    }

    return $this->render('book/update.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/remove/{id}', name: 'remove')]
    public function remove(BookRepository $repo,  ManagerRegistry $mr , $id): Response
    {
        $em=$mr->getManager();
        $book=$repo->find($id);
        $em->remove($book);
        $em->flush(); 

        return $this->redirectToRoute('listpub');
    }

    #[Route('/remove0', name: 'remove0')]
    public function deleteAuthorsWithZeroBooks(AuthorRepository $authorRepository): Response
    {
        $authorsWithZeroBooks = $authorRepository->findAuthorsWithZeroBooks1();

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($authorsWithZeroBooks as $author) {
            $entityManager->remove($author);
        }

        $entityManager->flush();

        return $this->redirectToRoute('listpub'); 
    }
    #[Route('/show/{id}', name: 'show')]
public function show( $id,BookRepository $bookRepository): Response
{
   
    $book = $bookRepository->find($id);
    return $this->render('book/show.html.twig', [
        'book' => $book,
    ]);
}

#[Route('/search', name: 'search')]
public function search(Request $request, BookRepository $bookRepository)
{
    $id = $request->query->get('id');

    $book = $bookRepository->searchBookById($id);

    return $this->render('book/search.html.twig', [
        'book' => $book,
    ]);
}

#[Route('/lba', name: 'lba')]
public function booksListByAuthors( BookRepository $bookRepository)
{
    $books = $bookRepository->booksListByAuthors();

    return $this->render('book/listbyauthor.html.twig', [
        'books' => $books,
    ]);
}

#[Route('/published', name: 'published')]
public function publishedBooks(BookRepository $bookRepository)
{
  
    $books = $bookRepository->publishedBooks();

    return $this->render('book/pubbooks.html.twig', [
        'books' => $books,
    ]);
}

//DQL
#[Route('/romance', name: 'romance')]
public function countRomanceBooks(BookRepository $bookRepository)
{
    $count = $bookRepository->countRomanceBooks();
    return $this->render('book/romance.html.twig', [
        'count' => $count,
    ]);
}

#[Route('/date', name: 'date')]
public function findBooksByDate(BookRepository $bookRepository): Response
{
    $books = $bookRepository->findBookByPublicationDate();

    return $this->render('book/pubbooks.html.twig', [
        'books' => $books,
    ]);
}

}
