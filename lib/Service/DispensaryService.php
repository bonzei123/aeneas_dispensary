<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Service;

use OCA\AeneasDispensary\Db\AbgabeMapper;
use OCP\AppFramework\Http;

class DispensaryService {

    private const DAILY_LIMIT  = 25; // g
    private const MONTHLY_LIMIT = 50; // g

    public function __construct(
        private AbgabeMapper $mapper
    ) {}

    public function addAbgabe(string $userId, int $amount): array {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Die Menge muss größer als 0 sein.');
        }

        $now = new \DateTimeImmutable('now');

        $daySum   = $this->mapper->getSumForUserDay($userId, $now);
        $monthSum = $this->mapper->getSumForUserMonth($userId, $now);

        $newDaySum   = $daySum + $amount;
        $newMonthSum = $monthSum + $amount;

        if ($newDaySum > self::DAILY_LIMIT) {
            throw new LimitExceededException('Tageslimit von 25g überschritten.');
        }

         if ($newMonthSum > self::MONTHLY_LIMIT) {
            throw new LimitExceededException('Monatslimit von 50g überschritten.');
        }

        $this->mapper->insertAbgabe($userId, $amount);

        return [
            'status' => Http::STATUS_OK,
            'day'    => $newDaySum,
            'month'  => $newMonthSum,
        ];
    }

    public function getUserStats(string $userId): array {
        $now = new \DateTimeImmutable('now');
        return [
            'day'   => $this->mapper->getSumForUserDay($userId, $now),
            'month' => $this->mapper->getSumForUserMonth($userId, $now),
            'dailyLimit'   => self::DAILY_LIMIT,
            'monthlyLimit' => self::MONTHLY_LIMIT,
        ];
    }

    public function getAdminList(): array {
        return $this->mapper->getAllForAdmin();
    }

    public function adminUpdateAbgabe(int $id, int $amount, string $adminId): array {
        $entity = $this->mapper->updateAbgabeAmount($id, $amount, $adminId);
        return [
            'id'        => $entity->getId(),
            'userId'    => $entity->getUserId(),
            'amount'    => $entity->getAmount(),
            'timestamp' => $entity->getTimestamp(),
            'editedBy'  => $entity->getEditedBy(),
            'editedAt'  => $entity->getEditedAt(),
        ];
    }
}