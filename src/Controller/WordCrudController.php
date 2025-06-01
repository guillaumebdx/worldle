<?php

namespace App\Controller;

use App\Entity\Word;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Lexique;
use Doctrine\ORM\EntityManagerInterface;

class WordCrudController extends AbstractCrudController
{
    private Lexique $lexique;

    public function __construct(Lexique $lexique)
    {
        $this->lexique = $lexique;
    }

    public static function getEntityFqcn(): string
    {
        return Word::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (get_class($entityInstance) !== self::getEntityFqcn()) {
            return;
        }

        if (!$this->lexique->isValid($entityInstance->getContent())) {
            $this->addFlash('danger', 'Ce mot n\'existe pas dans wikipedia.');

            return;
        }

        parent::persistEntity($entityManger, $entityInstance);
    }
}
