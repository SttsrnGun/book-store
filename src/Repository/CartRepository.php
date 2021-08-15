<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    // /**
    //  * @return Cart[] Returns an array of Cart objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    
    /**
     * @return Cart[] Returns an array of Book objects
    */
    public function findByUserBook($userId,$bookId = null)
    {
        $queryBuilder = $this->createQueryBuilder('cart')
            ->andWhere('cart.user = :userId')
            ->setParameter('userId', $userId)
            ->andWhere('cart.deletedAt is null')
            ->join('cart.book', 'book')
            ->select('cart','book');
        if($bookId){
            $queryBuilder = $queryBuilder->andWhere('book.id = :bookId')
                ->setParameter('bookId', $bookId)
                ->select('cart','book');
            ;
        }
        $queryBuilder = $queryBuilder->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $queryBuilder;
    }
}
