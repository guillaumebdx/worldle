<?php

namespace App\Service;

use App\Entity\LogicEntity\Statistic;
use App\Repository\AttemptRepository;
use DateTime;

class StatManager
{
    public const MAX_ATTEMPT = 6;

    private DateTime $date;

    private AttemptRepository $attemptRepository;

    public function __construct(AttemptRepository $attemptRepository, $date = null)
    {
        $this->date = $date ?? new DateTime();
        $this->attemptRepository = $attemptRepository;
    }

    public function buildStats() : Statistic
    {
        $statistic = new Statistic();
        $statistic->setSuccess($this->getSuccessCount())
            ->setAttempts($this->getAttemptCount())
            ->setFails($this->getFailCount());
        return $statistic;
    }

    public function getSuccessCount(): int
    {
        return count($this->attemptRepository
            ->findBy(['createdAt' => $this->date, 'isSuccess' => true]));
    }

    public function getFailCount(): int
    {
        return count($this->attemptRepository
            ->findBy(['createdAt' => $this->date, 'number' => self::MAX_ATTEMPT, 'isSuccess' => false]));
    }

    public function getAttemptCount(): int
    {
        return count($this->attemptRepository
            ->findBy(['createdAt' => $this->date]));
    }
}
