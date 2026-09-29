<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260929082543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip_month ADD CONSTRAINT FK_DDC6B0F5476C47F6 FOREIGN KEY (tip_id) REFERENCES tip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tip_month ADD CONSTRAINT FK_DDC6B0F5A0CBDE4 FOREIGN KEY (month_id) REFERENCES month (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP zip_code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip_month DROP FOREIGN KEY FK_DDC6B0F5476C47F6');
        $this->addSql('ALTER TABLE tip_month DROP FOREIGN KEY FK_DDC6B0F5A0CBDE4');
        $this->addSql('ALTER TABLE user ADD zip_code INT NOT NULL');
    }
}
