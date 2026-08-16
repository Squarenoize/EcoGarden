<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260816120738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip ADD title VARCHAR(255) NOT NULL, DROP tip_text');
        $this->addSql('ALTER TABLE tip ADD CONSTRAINT FK_4883B84CA0CBDE4 FOREIGN KEY (month_id) REFERENCES month (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip DROP FOREIGN KEY FK_4883B84CA0CBDE4');
        $this->addSql('ALTER TABLE tip ADD tip_text LONGTEXT NOT NULL, DROP title');
    }
}
