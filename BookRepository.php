<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//QB
public function searchBookById($id)
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function booksListByAuthors()
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.author', 'a')
        ->orderBy('a.username', 'ASC')
        ->getQuery()
        ->getResult();
}
public function publishedBooks()
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a') 
        ->andWhere('b.published = true')
        ->andWhere('b.publicationDate < :year')
        ->andWhere('a.nbbooks > 10') 
        ->setParameter('year', new \DateTime('2023-01-01'))
        ->getQuery()
        ->getResult();
}

//DQL

public function countRomanceBooks()
    {
        $em = $this->getEntityManager();

        $dql = 'SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.category = :category';
        $query = $em->createQuery($dql)->setParameter('category', 'Romance');

        return $query->getSingleScalarResult();
    }

    public function findBookByPublicationDate()
    {
        $em = $this->getEntityManager();
    
        return $em->createQuery('SELECT b FROM App\Entity\Book b WHERE b.publicationDate BETWEEN :start_date AND :end_date')
            ->setParameter('start_date', '2018-01-01')
            ->setParameter('end_date', '2020-12-31')
            ->getResult();
    }
    

}





