<?php

namespace App\Controller;

use App\Repository\WordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WordController extends AbstractController
{
    /**
     * @Route("/", name="word")
     */
    public function index(WordRepository $wordRepository): Response
    {
        $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        $letters      = str_split($wordOfTheDay->getContent());
        $keyboard1    = str_split('AZERTYUIOP');
        $keyboard2    = str_split('QSDFGHJKL');
        $keyboard3    = str_split('WXCVBNM');
        return $this->render('word/index.html.twig', [
            'word'     => $wordOfTheDay,
            'letters'  => $letters,
            'keyboard1' => $keyboard1,
            'keyboard2' => $keyboard2,
            'keyboard3' => $keyboard3,
        ]);
    }

    /**
     * @Route("/check/{word}", name="check_word", methods="GET")
     */
    public function checkWord(string $word, WordRepository $wordRepository)
    {
        $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        $lettersOfTheDay = str_split($wordOfTheDay->getContent());
        $letters = str_split($word);
        $result = [];
        foreach ($letters as $key => $letter) {
            if ($letter === $lettersOfTheDay[$key]) {
                $result[] = 'green';
            } elseif (in_array($letter, $lettersOfTheDay)) {
                $result[] = 'yellow';
            } else {
                $result[] = 'blue';
            }

        }
        $response = [];
        $response['result'] = $result;
        $response['success'] = false;
        if ($wordOfTheDay->getContent() === $word) {
            $response['success'] = true;
        }
        return new JsonResponse($response);
    }
}
