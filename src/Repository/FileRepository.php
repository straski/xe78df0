<?php

namespace App\Repository;

use App\{Config\ParseState, Entity\Document, Entity\File};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends AbstractServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findOneBySha1($sha1): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.sha1 = :val')
            ->setParameter('val', $sha1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextById(string $id): ?File
    {
        if (!uuid_is_valid($id)) {
            return null;
        }

        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->andWhere('f.parseState = :val')
            ->setParameter('id', $id)
            ->setParameter('val', ParseState::Queued)
            ->orderBy('f.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
