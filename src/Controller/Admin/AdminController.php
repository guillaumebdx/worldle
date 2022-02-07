<?php

namespace App\Controller\Admin;

use App\Entity\Attempt;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/tentatives/{date}", name="attempt")
     */
    public function attempt(PaginatorInterface $paginator, Request $request, $date = null)
    {
        $date = $date ? new \DateTime($date) : new \DateTime();
        $attempts = $this->managerRegistry
            ->getRepository(Attempt::class)
            ->findBy(['createdAt' => $date], ['id' => 'DESC']);
        $pagination = $paginator->paginate($attempts, $request->query->getInt('page', 1),100);
        return $this->render('admin/attempt.html.twig', [
            'pagination' => $pagination,
            'attempts' => $attempts,
            'date'     => $date,
        ]);
    }
}
