<?php

namespace App\Controller;

use App\Repository\WordRepository;
use App\Service\ColorManager;
use App\Service\Lexique;
use App\Service\SessionHandler;
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
        $lastDate = $session->get('time');
        $now = new \DateTime();
        if ($lastDate && $lastDate->format('d') !== $now->format('d') ) {
            foreach ($session->all() as $sessionType => $value) {
               $session->remove($sessionType);
            }
        }
        $inWorkingLines = $session->get('lines');
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
                              Lexique $lexique,
                              ColorManager $colorManager,
                              SessionHandler $sessionHandler)
    {
        $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        $response              = [];
        $response['result']    = $colorManager->build($word, $wordOfTheDay->getContent());
        $response['success']   = false;
        $response['errors']    = $colorManager->getErrors();
        $response['valids']    = $colorManager->getValids();
        $response['aways']     = $colorManager->getAways();
        $response['validWord'] = $lexique->isValid(strtolower($word));
        if ($wordOfTheDay->getContent() === $word) {
            $response['success'] = true;
        }
        if ($response['validWord']) {
            $sessionHandler->write($response, $word);
        }
        return new JsonResponse($response);
    }
}
