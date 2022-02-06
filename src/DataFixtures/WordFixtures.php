<?php

namespace App\DataFixtures;

use App\Entity\Word;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WordFixtures extends Fixture
{
    public const WORDS = [
        [
            'content' => 'BERLIN',
        ],
        [
            'content' => 'CHICAGO',
        ],
        [
            'content' => 'MONACO',
        ],
        [
            'content' => 'BORDEAUX',
        ],
        [
            'content' => 'NAPLES',
        ],
        [
            'content' => 'MADRID',
        ],
        [
            'content' => 'QUEBEC',
        ],
        [
            'content' => 'TOKYO',
        ],
        [
            'content' => 'MEXIQUE',
        ],
    ];

    public const VIP_WORDS = [
        [
            'content' => 'USA',
        ],
        [
            'content' => 'AFRIQUE',
        ],
        [
            'content' => 'SERBIE',
        ],
        [
            'content' => 'CORSE',
        ],
        [
            'content' => 'PEROU',
        ],
        [
            'content' => 'MALI',
        ],
        [
            'content' => 'CONGO',
        ],
        [
            'content' => 'URUGUAY',
        ],
        [
            'content' => 'ANGERS',
        ],
    ];
    public function load(ObjectManager $manager): void
    {
        foreach (self::WORDS as $key => $wordData) {
            $word = new Word();
            $word->setContent($wordData['content']);
            $word->setDefinition('à définir');
            $today = new \DateTime();
            $today->add(new \DateInterval('P' . $key . 'D'));
            $word->setPlayAt($today);
            $manager->persist($word);
        }
        foreach (self::VIP_WORDS as $key => $wordData) {
            $word = new Word();
            $word->setContent($wordData['content']);
            $word->setDefinition('à définir');
            $word->setIsVip(true);
            $today = new \DateTime();
            $today->add(new \DateInterval('P' . $key . 'D'));
            $word->setPlayAt($today);
            $manager->persist($word);
        }
        $manager->flush();
    }
}
