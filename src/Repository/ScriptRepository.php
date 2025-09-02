<?php

namespace OHMedia\ScriptBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\ScriptBundle\Entity\Script;

/**
 * @method Script|null find($id, $lockMode = null, $lockVersion = null)
 * @method Script|null findOneBy(array $criteria, array $orderBy = null)
 * @method Script[]    findAll()
 * @method Script[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Script::class);
    }

    public function save(Script $script, bool $flush = false): void
    {
        $this->getEntityManager()->persist($script);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Script $script, bool $flush = false): void
    {
        $this->getEntityManager()->remove($script);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
