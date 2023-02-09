<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $string string to be searched for
     * @return array< Product>
     */
    public function searchOnNameAndReference(string $string): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name like :likeSearch')
            ->orWhere('p.reference = :search')
            ->setParameter('search', $string)
            ->setParameter('likeSearch', '%'.$string.'%')
            ->orderBy('p.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
