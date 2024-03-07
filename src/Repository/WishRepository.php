<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;


class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function getWishById(int $id): ?Wish
    {
        $query = $this->createQueryBuilder('wish');
        $query->where('wish.id = :id');
        $query->orderBy('wish.dateCreated', 'DESC');
        $query->setParameter('id', $id);
        return $query->getQuery()->getSingleResult();
    }

    public function getAllPublishedWishes()
    {
        $query = $this->createQueryBuilder('wish');
        $query->where('wish.isPublished = 1');
        $query->orderBy('wish.dateCreated', 'DESC');
        return $query->getQuery()->getResult();
    }

    public function getWishesByPage(int $page, int $elementsByPage): array
    {
        $query = $this->createQueryBuilder('wish');
        $query->where('wish.isPublished = 1');
        $query->orderBy('wish.dateCreated', 'DESC');
        $query->setMaxResults($elementsByPage);
        $query->setFirstResult(($page - 1) * $elementsByPage);
        return $query->getQuery()->getResult();
    }

    public function getWishesWithCategoryByPage(int $page, int $elementsByPage): array
    {
        return $this->createQueryBuilder('wish')
            ->where('wish.isPublished = 1')
            ->addOrderBy('wish.dateCreated', 'DESC')
            ->leftJoin('wish.category', 'category')
            ->addSelect('category')
            ->setMaxResults($elementsByPage)
            ->setFirstResult(($page - 1) * $elementsByPage)
            ->getQuery()
            ->getResult();
    }
}