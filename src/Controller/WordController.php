<?php

namespace App\Controller;

use App\Repository\WordRepository;
use App\Service\Lexique;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class WordController extends AbstractController
{

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="word")
     */
    public function index(WordRepository $wordRepository, RequestStack $requestStack): Response
    {
        $session = $this->requestStack->getSession();
        $inWorkingLines = $session->get('lines');
        $lastDate = $session->get('time');
        $now = new \DateTime();
        if ($lastDate && $lastDate->diff($now)->i > 1) {
            dump($lastDate->diff($now)->i > 1);
            $session->set('lines', null);
            $session->set('time', null);
        }
        dump(
            $this->requestStack->getSession()->get('lines'),
            $this->requestStack->getSession()->get('time'),
            $this->requestStack->getSession()->get('colors'));
        $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        $letters      = str_split($wordOfTheDay->getContent());
        $keyboard1    = str_split('AZERTYUIOP');
        $keyboard2    = str_split('QSDFGHJKL');
        $keyboard3    = str_split('WXCVBNM');
        return $this->render('word/index.html.twig', [
            'in_working_lines' => $inWorkingLines,
            'in_working_colors' => $this->requestStack->getSession()->get('colors'),
            'word'             => $wordOfTheDay,
            'letters'          => $letters,
            'keyboard1'        => $keyboard1,
            'keyboard2'        => $keyboard2,
            'keyboard3'        => $keyboard3,
        ]);
    }

    /**
     * @Route("/check/{word}", name="check_word", methods="GET")
     */
    public function checkWord(string $word,
                              WordRepository $wordRepository,
                              RequestStack $requestStack,
                              Lexique $lexique)
    {
        $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        $lettersOfTheDay = str_split($wordOfTheDay->getContent());
        $letters = str_split($word);
        $result = [];
        $errors = [];
        $valids = [];
        $aways = [];

        foreach ($letters as $key => $letter) {
            if ($letter === $lettersOfTheDay[$key]) {
                $result[] = 'green';
                $valids[] = $letter;
            } else {
                $result[] = 'blue';
                $errors[] = $letter;
            }
        }
        foreach ($letters as $key => $letter) {
            if (!in_array($letter, $valids) && !in_array($letter, $aways) && in_array($letter, $lettersOfTheDay)) {
                $result[$key] = 'yellow';
                $aways[] = $letter;
            }
        }

        $response = [];
        $response['result'] = $result;
        $response['success'] = false;
        $response['errors'] = $errors;
        $response['valids'] = $valids;
        $response['aways'] = $aways;
        $response['validWord'] = $lexique->isValid(strtolower($word));
        if ($wordOfTheDay->getContent() === $word) {
            $response['success'] = true;
        }
        if ($response['validWord']) {
            $session = $requestStack->getSession();
            if ($session->get('lines')) {
                $lines = $session->get('lines');
                $lines[] = str_split($word);
                $session->set('lines', $lines);
                $colors = $session->get('colors');
                $colors[] = $response['result'];
                $session->set('colors', $colors);
                $session->set('time', new \DateTime());
            } else {
                $session->set('lines', [str_split($word)]);
                $session->set('colors', [$result]);
                $session->set('time', new \DateTime());
            }
        }
        return new JsonResponse($response);
    }
}
