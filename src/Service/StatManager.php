<?php

namespace App\Service;

use App\Entity\LogicEntity\Statistic;
use App\Repository\AttemptRepository;
use DateTime;

class StatManager
{
    public const MAX_ATTEMPT = 6;

    private ?DateTime $date;

    private AttemptRepository $attemptRepository;

    private bool $isVip;

    public function __construct(AttemptRepository $attemptRepository, $date = null)
    {
        $this->date = $date ?? new DateTime();
        $this->attemptRepository = $attemptRepository;
    }

    public function buildStats($isVip = false) : Statistic
    {
        $this->isVip = $isVip;
        $statistic = new Statistic();
        $statistic->setSuccess($this->getSuccessCount())
            ->setAttempts($this->getAttemptCount())
            ->setFails($this->getFailCount())
            ->setSuccessByAttempts($this->getSuccessByAttempts())
        ;
        return $statistic;
    }

    public function getSuccessByAttempts(): array
    {
        $results = [];
        for ($i = 1; $i <= self::MAX_ATTEMPT; $i++) {
            $results[$i] = $this->attemptRepository->getSuccessByAttempts($this->date, $this->isVip, $i);
        }
        return $results;
    }

    public function getSuccessCount(): int
    {
        return $this->attemptRepository->getSuccessCount($this->date, $this->isVip);
    }

    public function getFailCount(): int
    {
        return $this->attemptRepository->getFailCount($this->date, $this->isVip);
    }

    public function getAttemptCount(): int
    {
        return $this->attemptRepository->getAttemptCount($this->date, $this->isVip);
    }
}
