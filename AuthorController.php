<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/fetch', name: 'fetch')]
    public function fetch(AuthorRepository $repo): Response
    {
        $result=$repo->findAll();
        return $this->render('author/test.html.twig',[
            'response'=>$result,
        ]);
    }

    #[Route('/add', name: 'add')]
public function add(  ManagerRegistry $mr ):Response
{   

    $a=new Author(); 
    $a->setUserName('test');
    $a->setEmail('test@gmail.com');
    $em=$mr->getManager(); //3-persist-flush
    $em->persist($a);
    $em->flush();
   
    
    return $this->redirectToRoute('fetch');

}

#[Route('/addf', name: 'addf')]
public function addf(  ManagerRegistry $mr, Request $req ):Response
{   
    $a=new Author(); 
     $form=$this->createForm(AuthorType::class,$a); 
     $form->handleRequest ($req);
     if( $form->isSubmitted())
     {
    $em=$mr->getManager(); 
    $em->persist($a);
    $em->flush();
    return $this->redirectToRoute('fetch');
    }
    return $this->render('author/add.html.twig', [
        'f' => $form->createView(),
    ]);
}

#[Route('/removef/{id}', name: 'removef')]
    public function removef(AuthorRepository $repo,  ManagerRegistry $mr , $id): Response
    {
        $em=$mr->getManager();
        $author=$repo->find($id);
        $em->remove($author);
        $em->flush(); 

        return $this->redirectToRoute('fetch');
    }



#[Route('/updatef/{id}', name: 'update')]
 
   public function updatef(Request $request, $id,ManagerRegistry $mr,AuthorRepository $repo ):Response
{
    $author = $repo->find($id);
    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($request);
    if ($form->isSubmitted() ) {
        $em=$mr->getManager();
        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('fetch');
    }

    return $this->render('author/update.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/list', name: 'list')]
public function listAuthorsByEmail(AuthorRepository $repo):Response
{ $result=$repo->listAuthorsByEmail();
    
    return $this->render('author/test.html.twig', [
        'response' => $result
    ]);
}

#[Route('/searchauthors', name: 'searchauthors')]
public function searchAuthors(Request $request, AuthorRepository $authorRepository): Response
{
    $minBooks = $request->get('min_books');
    $maxBooks = $request->get('max_books');

    $authors = $authorRepository->findAuthorsByBookCountRange($minBooks, $maxBooks);

    return $this->render('author/searchauthor.html.twig', [
        'authors' => $authors,
    ]);
}

#[Route('/delete0', name: 'delete0')]
public function deleteAuthorsWithZeroBooks(AuthorRepository $authorRepository, EntityManagerInterface $entityManager): Response
{
    $authorsToDelete = $authorRepository->findAuthorsWithZeroBooks();

    foreach ($authorsToDelete as $author) {
        $entityManager->remove($author);
    }

    $entityManager->flush();

    return $this->redirectToRoute('fetch');
}

}
