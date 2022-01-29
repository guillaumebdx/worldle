<?php

namespace App\Entity\LogicEntity;

class Statistic
{
    private int $success;

    private int $attempts;

    private int $fails;

    private array $successByAttempts;

    /**
     * @return int
     */
    public function getAverage(): int
    {
        $average = 0;
        if ($this->success + $this->fails > 0) {
            $average = $this->success / ($this->success + $this->fails) * 100;
        }
        return $average;
    }

    /**
     * @return array
     */
    public function getSuccessByAttempts(): array
    {
        return $this->successByAttempts;
    }

    /**
     * @param array $successByAttempts
     * @return Statistic
     */
    public function setSuccessByAttempts(array $successByAttempts): Statistic
    {
        $this->successByAttempts = $successByAttempts;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @param int $success
     * @return Statistic
     */
    public function setSuccess(int $success): Statistic
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     * @return Statistic
     */
    public function setAttempts(int $attempts): Statistic
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * @return int
     */
    public function getFails(): int
    {
        return $this->fails;
    }

    /**
     * @param int $fails
     * @return Statistic
     */
    public function setFails(int $fails): Statistic
    {
        $this->fails = $fails;
        return $this;
    }


}
