<?php

namespace App\Service;

class ColorManager
{
    private $valids = [];

    private $errors = [];

    private $aways = [];


    public function build(string $attempt, string $expected): array
    {
        $result = [];
        $valids = [];
        $nbYellowInResult = [];
        for ($i=0; $i<strlen($attempt); $i++) {
            if ($attempt[$i] === $expected[$i]) {
                $result[] = 'green';
                $valids[] = $attempt[$i];
                $this->valids[] = $attempt[$i];
                $nbYellowInResult[$attempt[$i]] = $nbYellowInResult[$attempt[$i]] ?? 0;
                $nbYellowInResult[$attempt[$i]]++;
            } else {
                $result[] = 'blue';
                $this->errors[] = $attempt[$i];
            }
        }
        for ($i=0; $i<strlen($attempt); $i++) {
            if ($attempt[$i] !== $expected[$i]) {
                if (in_array($attempt[$i], str_split($expected))) {
                   $nbYellowInResult[$attempt[$i]] = $nbYellowInResult[$attempt[$i]] ?? 0;
                    if ($nbYellowInResult[$attempt[$i]] < array_count_values(str_split($expected))[$attempt[$i]]) {
                        $result[$i] = 'yellow';
                        $this->aways[] = $attempt[$i];
                        $nbYellowInResult[$attempt[$i]]++;
                    }
                }
            }
        }
        return $result;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getValids()
    {
        return $this->valids;
    }

    public function getAways()
    {
        return $this->aways;
    }
}
