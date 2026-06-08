<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class AbgabeMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'aeneas_abgabe', Abgabe::class);
    }

    public function getSumForUserDay(string $userId, \DateTimeImmutable $day): int {
        $start = $day->setTime(0, 0, 0)->getTimestamp();
        $end   = $day->setTime(23, 59, 59)->getTimestamp();

        $qb = $this->db->getQueryBuilder();
        $qb->select($qb->createFunction('COALESCE(SUM(`amount`), 0)'))
            ->from('aeneas_abgabe')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->andWhere($qb->expr()->gte('timestamp', $qb->createNamedParameter($start)))
            ->andWhere($qb->expr()->lte('timestamp', $qb->createNamedParameter($end)));

        return (int)$qb->executeQuery()->fetchOne();
    }

    public function getSumForUserMonth(string $userId, \DateTimeImmutable $day): int {
        $start = $day->modify('first day of this month')->setTime(0, 0, 0)->getTimestamp();
        $end   = $day->modify('last day of this month')->setTime(23, 59, 59)->getTimestamp();

        $qb = $this->db->getQueryBuilder();
        $qb->select($qb->createFunction('COALESCE(SUM(`amount`), 0)'))
            ->from('aeneas_abgabe')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->andWhere($qb->expr()->gte('timestamp', $qb->createNamedParameter($start)))
            ->andWhere($qb->expr()->lte('timestamp', $qb->createNamedParameter($end)));

        return (int)$qb->executeQuery()->fetchOne();
    }

    public function insertAbgabe(string $userId, int $amount): Abgabe {
        $abgabe = new Abgabe();
        $abgabe->setUserId($userId);
        $abgabe->setAmount($amount);
        $abgabe->setTimestamp(time());

        return $this->insert($abgabe);
    }

    public function getAllForAdmin(): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('aeneas_abgabe')
            ->orderBy('timestamp', 'DESC');

        return $this->findEntities($qb);
    }

    public function updateAbgabeAmount(int $id, int $newAmount, string $adminId): Abgabe {
        /** @var Abgabe $abgabe */
        $abgabe = $this->find($id);
        $abgabe->setAmount($newAmount);
        $abgabe->setEditedBy($adminId);
        $abgabe->setEditedAt(time());

        return $this->update($abgabe);
    }
}
