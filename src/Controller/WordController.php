<?php

namespace App\Controller;

use App\Entity\Attempt;
use App\Entity\Word;
use App\Repository\WordRepository;
use App\Service\ColorManager;
use App\Service\Lexique;
use App\Service\SessionHandler;
use App\Service\StatManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class WordController extends AbstractController
{

    private RequestStack $requestStack;

    private ManagerRegistry $managerRegistry;

    public function __construct(RequestStack $requestStack, ManagerRegistry $managerRegistry)
    {
        $this->requestStack = $requestStack;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/error-word", name="error-word")
     */
    public function errorWord()
    {
        //TODO vérifier s'il est VIP en JS pour gérer la redirection vers word ou word + param
        $this->addFlash('danger', 'La page a été rafraichie car le mot a changé.');
        return $this->redirectToRoute('word');
    }

    /**
     * @Route("/{area}", name="word")
     */
    public function index(WordRepository $wordRepository,
                          StatManager $statManager,
                          SessionHandler $sessionHandler,
                          $area = ''): Response
    {
        $sessionHandler->removeSessionOnNewType($area);
        $sessionHandler->removeSessionOnNewDay();
        $inWorkingLines = $this->requestStack->getSession()->get('lines');
        if ($area === 'vip-area') {
            $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime(), 'isVip' => true]);
        } else {
            $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        }
        $letters      = str_split($wordOfTheDay->getContent());
        $keyboard1    = str_split('AZERTYUIOP');
        $keyboard2    = str_split('QSDFGHJKL');
        $keyboard3    = str_split('WXCVBNM');

        $secondsToTomorrow = strtotime('tomorrow') - time();
        return $this->render('word/index.html.twig', [
            'in_working_lines' => $inWorkingLines,
            'in_working_colors' => $this->requestStack->getSession()->get('colors'),
            'word'             => $wordOfTheDay,
            'letters'          => $letters,
            'keyboard1'        => $keyboard1,
            'keyboard2'        => $keyboard2,
            'keyboard3'        => $keyboard3,
            'stats'            => $statManager->buildStats($area === 'vip-area'),
            'seconds'          => $secondsToTomorrow,
            'is_vip'           => $area === 'vip-area',
        ]);
    }

    /**
     * @Route("/check/{word}/{attemptNumber}", name="check_word", methods="GET")
     */
    public function checkWord(string $word,
                              int $attemptNumber,
                              WordRepository $wordRepository,
                              Lexique $lexique,
                              ColorManager $colorManager,
                              SessionHandler $sessionHandler)
    {
        $area = $this->requestStack->getSession()->get('area');
        if ($area === 'vip') {
            $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime(), 'isVip' => true]);
        } else {
            $wordOfTheDay = $wordRepository->findOneBy(['playAt' => new \DateTime()]);
        }
        if (strlen($wordOfTheDay->getContent()) !== strlen($word)) {
            return new JsonResponse(['wordServer' => $wordOfTheDay->getContent()]);
        }
        $response              = [];
        $response['result']    = $colorManager->build($word, $wordOfTheDay->getContent());
        $response['success']   = false;
        $response['errors']    = $colorManager->getErrors();
        $response['valids']    = $colorManager->getValids();
        $response['aways']     = $colorManager->getAways();
        $response['validWord'] = $lexique->isValid(strtolower($word));
        $attempt = new Attempt();
        $attempt->setContent($word);
        $attempt->setWord($wordOfTheDay);
        if ($wordOfTheDay->getContent() === $word) {
            $response['success'] = true;
        }
        $attempt->setIsSuccess($response['success']);
        $attempt->setIsValid($response['validWord']);
        $attempt->setNumber($attemptNumber);
        if ($response['validWord']) {
            $sessionHandler->write($response, $word);
        }
        $this->managerRegistry->getManager()->persist($attempt);
        $this->managerRegistry->getManager()->flush();
        $response['wordServer'] = $wordOfTheDay->getContent();
        return new JsonResponse($response);
    }
}
