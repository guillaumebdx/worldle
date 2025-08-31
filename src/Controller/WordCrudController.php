<?php

namespace App\Controller;

use App\Entity\Word;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Lexique;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Service\WordManager;

class WordCrudController extends AbstractCrudController
{
    private Lexique $lexique;
    private WordManager $wordManager;

    public function __construct(
        Lexique $lexique,
        WordManager $wordManager
        )
    {
        $this->lexique = $lexique;
        $this->wordManager = $wordManager;
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

        $existingWords = $entityManager->getRepository(Word::class)->findBy(['content' => $entityInstance->getContent()]);
    
        if ($existingWords) {
            if (count($existingWords) === 1) {
                $dateText = sprintf("le %s", $existingWords[0]->getPlayAt()->format('d/m/Y'));
            } else {
                $dateText = sprintf("aux dates suivantes : %s", implode(', ', array_map(function($word) {
                    return $word->getPlayAt()->format('d/m/Y');
                }, $existingWords)));
            }

            $this->addFlash('warning', sprintf('Pour information, ce mot a déjà été joué %s.', $dateText));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
    
    public function configureCrud(Crud $crud): Crud 
    {
       return $crud->setDefaultSort(['playAt' => 'desc']);
    }

    public function createEntity(string $entityFqcn)
    {
        $nextDateToFill = $this->wordManager->getNextDateToFill();

        $word = new Word();
        $word->setPlayAt($nextDateToFill);

        return $word;
    }
}
