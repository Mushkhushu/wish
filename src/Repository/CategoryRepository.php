<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function howManyWishesByCategory() {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.wishes', 'w')
            ->addSelect('w')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function getWishesByCategory(int $id) {
        $qb = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id) // Lier le paramètre ':id'
            ->select('c.name AS category_name', 'w.title AS wish_title', 'w.picture AS wish_picture')
            ->leftJoin('c.wishes', 'w')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
