<?php

namespace OHMedia\ScriptBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\WysiwygBundle\Repository\WysiwygRepositoryInterface;

/**
 * @method Script|null find($id, $lockMode = null, $lockVersion = null)
 * @method Script|null findOneBy(array $criteria, array $orderBy = null)
 * @method Script[]    findAll()
 * @method Script[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScriptRepository extends ServiceEntityRepository implements WysiwygRepositoryInterface
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

    public function getActive(): ?Script
    {
        return $this->createQueryBuilder('a')
            ->where('a.starts_at IS NOT NULL')
            ->andWhere('a.starts_at < :now')
            ->andWhere('(a.ends_at IS NULL OR a.ends_at > :now)')
            ->setParameter('now', DateTimeUtil::getDateTimeUtc())
            ->orderBy('a.starts_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getShortcodeQueryBuilder(string $shortcode): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.content LIKE :shortcode')
            ->setParameter('shortcode', '%'.$shortcode.'%');
    }

    public function getShortcodeRoute(): string
    {
        return 'script_edit';
    }

    public function getShortcodeRouteParams(mixed $entity): array
    {
        return ['id' => $entity->getId()];
    }

    public function getShortcodeHeading(): string
    {
        return 'Scripts';
    }

    public function getShortcodeLinkText(mixed $entity): string
    {
        return sprintf(
            '%s (ID:%s)',
            (string) $entity,
            $entity->getId(),
        );
    }
}
