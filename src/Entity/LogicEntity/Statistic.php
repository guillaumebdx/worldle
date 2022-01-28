<?php

namespace App\Entity\LogicEntity;

class Statistic
{
    private int $success;

    private int $attempts;

    private int $fails;

    private int $average;

    /**
     * @return int
     */
    public function getAverage(): int
    {
        return $this->fails / ($this->success + $this->fails) * 100;
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
