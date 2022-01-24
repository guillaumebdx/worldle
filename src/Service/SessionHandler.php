<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionHandler
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function write($response, $word)
    {
        $session = $this->requestStack->getSession();
        if ($session->get('lines')) {
            $lines = $session->get('lines');
            $lines[] = str_split($word);
            $session->set('lines', $lines);
            $colors = $session->get('colors');
            $colors[] = $response['result'];
            $session->set('colors', $colors);
            $session->set('errors', array_unique(array_merge($session->get('errors'), $response['errors'])));
            $session->set('valids', array_unique(array_merge($session->get('valids'), $response['valids'])));
            $session->set('aways', array_unique(array_merge($session->get('aways'), $response['aways'])));
            $session->set('time', new \DateTime());
        } else {
            $session->set('lines', [str_split($word)]);
            $session->set('colors', [$response['result']]);
            $session->set('errors', $response['errors']);
            $session->set('valids', $response['valids']);
            $session->set('aways', $response['aways']);
            $session->set('time', new \DateTime());
        }
        $session->set('success', $response['success']);
    }
}
