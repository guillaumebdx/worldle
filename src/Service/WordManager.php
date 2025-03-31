<?php

namespace App\Service;

use App\Repository\WordRepository;

class WordManager 
{
    public function __construct(
        private WordRepository $wordRepository
    )
    {
    }

    public function getNextDateToFill(): \DateTime
    {
        $date = new \DateTime('yesterday');
        $wordIsComplete = true;

        while ($wordIsComplete) {
            $date->modify('+1 day');
            $wordIsComplete = $this->wordRepository->isCompletedDate($date);
        }

        return $date;
    }
}