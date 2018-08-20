<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180820070236 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meal_schedule ADD account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE meal_schedule ADD CONSTRAINT FK_3E2A0A899B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_3E2A0A899B6B5FBA ON meal_schedule (account_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meal_schedule DROP FOREIGN KEY FK_3E2A0A899B6B5FBA');
        $this->addSql('DROP INDEX IDX_3E2A0A899B6B5FBA ON meal_schedule');
        $this->addSql('ALTER TABLE meal_schedule DROP account_id');
    }
}
