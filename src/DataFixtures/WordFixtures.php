<?php

namespace App\DataFixtures;

use App\Entity\Word;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WordFixtures extends Fixture
{
    public const WORDS = [
        [
            'content' => 'FRANCE',
            'definition' => 'La France, en forme longue depuis 1875 la République française, est un État souverain transcontinental dont le territoire métropolitain est situé en Europe de l\'Ouest et dont le territoire ultramarin est situé dans les océans Indien, Atlantique et Pacifique ainsi qu\'en Amérique du Sud.',
        ],
        [
            'content' => 'PARIS',
            'definition' => 'Paris est la commune la plus peuplée et la capitale de la France.'
        ],
        [
            'content' => 'GUATEMALA',
            'definition' => 'Le Guatemala, ou Guatémala, en forme longue la république du Guatemala (en espagnol : República de Guatemala), est un pays d\'Amérique centrale entouré par le Mexique, le Belize, la mer des Caraïbes, le Honduras, le Salvador et l\'océan Pacifique',
        ],
    ];
    public function load(ObjectManager $manager): void
    {
        foreach (self::WORDS as $wordData) {
            $word = new Word();
            $word->setContent($wordData['content']);
            $word->setDefinition($wordData['definition']);
            $today = new \DateTime();
            $word->setPlayAt($today);
            $manager->persist($word);
        }
        $manager->flush();
    }
}
