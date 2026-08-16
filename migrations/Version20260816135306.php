<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260816135306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tip_month (tip_id INT NOT NULL, month_id INT NOT NULL, INDEX IDX_DDC6B0F5476C47F6 (tip_id), INDEX IDX_DDC6B0F5A0CBDE4 (month_id), PRIMARY KEY(tip_id, month_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tip_month ADD CONSTRAINT FK_DDC6B0F5476C47F6 FOREIGN KEY (tip_id) REFERENCES tip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tip_month ADD CONSTRAINT FK_DDC6B0F5A0CBDE4 FOREIGN KEY (month_id) REFERENCES month (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_4883B84CA0CBDE4 ON tip');
        $this->addSql('ALTER TABLE tip DROP month_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip_month DROP FOREIGN KEY FK_DDC6B0F5476C47F6');
        $this->addSql('ALTER TABLE tip_month DROP FOREIGN KEY FK_DDC6B0F5A0CBDE4');
        $this->addSql('DROP TABLE tip_month');
        $this->addSql('ALTER TABLE tip ADD month_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_4883B84CA0CBDE4 ON tip (month_id)');
    }
}
