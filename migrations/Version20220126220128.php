<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220126220128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attempt ADD word_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attempt ADD CONSTRAINT FK_18EC0266E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('CREATE INDEX IDX_18EC0266E357438D ON attempt (word_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attempt DROP FOREIGN KEY FK_18EC0266E357438D');
        $this->addSql('DROP INDEX IDX_18EC0266E357438D ON attempt');
        $this->addSql('ALTER TABLE attempt DROP word_id');
    }
}
