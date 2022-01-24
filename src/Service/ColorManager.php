<?php

namespace App\Service;

class ColorManager
{
    private $valids = [];

    private $errors = [];

    private $aways = [];

    private $attempt;

    private $expected;

    private $expecteds;

    private $countLetters;

    private $buildAways;

    private $firstCountValids = [];

    public function build(string $attempt, string $expected): array
    {
        $this->attempt = $attempt;
        $this->expected = $expected;
        $this->expecteds = str_split($expected);
        $this->countLetters = array_count_values($this->expecteds);
        $this->buildAways = $this->buildKeysFromLetters();
        $this->firstCountValids = $this->buildKeysFromLetters();
        $this->firstCountValidsLetter();
        $result = [];
        for ($i = 0; $i < strlen($attempt); $i++) {
            $result[] = $this->letterToColor($attempt[$i], $i);
        }
        return $result;
    }

    public function firstCountValidsLetter(): void
    {
        for ($i = 0; $i < strlen($this->attempt); $i++) {
            if ($this->attempt[$i] === $this->expected[$i]) {
                $this->firstCountValids[$this->attempt[$i]]++;
            }
        }
    }

    public function buildKeysFromLetters()
    {
        $result = [];
        foreach ($this->expecteds as $letter) {
            $result[$letter] = 0;
        }
        return $result;
    }

    public function letterToColor(string $letter, $position)
    {
        if ($letter === $this->expected[$position]) {
            $result = 'green';
            $this->valids[] = $letter;
        } elseif (in_array($letter, $this->expecteds)) {
            $result = $this->checkIfYellowOrBlue($letter);
        } else {
            $result = 'blue';
            $this->errors[] = $letter;
        }
        return $result;
    }

    public function checkIfYellowOrBlue(string $letter)
    {
        if ($this->notMoreAwayThanTotal($letter) && $this->notMoreGreenThanTotal($letter)) {
            $this->buildAways[$letter]++;
            $this->aways[] = $letter;
            return 'yellow';
        } else {
            $this->errors[] = $letter;
            return 'blue';
        }
    }

    public function notMoreAwayThanTotal($letter)
    {
        return $this->buildAways[$letter] < $this->countLetters[$letter];
    }

    public function notMoreGreenThanTotal($letter)
    {
        $result = false;
        if ($this->firstCountValids[$letter] < $this->buildAways[$letter] + $this->countLetters[$letter]) {
            $result = true;
        }
        return $result;
    }

    public function hasOneOccurence($letter): bool
    {
        return $this->countLetters[$letter] === 1;
    }

    public function getAways(): array
    {
        return $this->aways;
    }

    public function getValids(): array
    {
        return $this->valids;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
