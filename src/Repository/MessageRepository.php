<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * Custom methods:
 * @method Message[] findByOptionalStatus(?string $status)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Returns messages filtered by status if provided.
     *
     * @param string|null $status
     * @return Message[]
     */
    public function findByOptionalStatus(?string $status): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($status) {
            $qb->where('m.status = :status')
               ->setParameter('status', $status);
        }

        /** @var Message[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
