<?php

namespace App\Controller\Admin;

use App\Entity\Attempt;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function attempt($date = null)
    {
        $date = $date ? new \DateTime($date) : new \DateTime();
        $attempts = $this->managerRegistry
            ->getRepository(Attempt::class)
            ->findBy(['createdAt' => $date], ['id' => 'DESC']);
        return $this->render('admin/attempt.html.twig', [
            'attempts' => $attempts,
            'date'     => $date,
        ]);
    }
}
