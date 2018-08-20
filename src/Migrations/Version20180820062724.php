<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180820062724 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meal_schedule (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, breakfast INT DEFAULT NULL, middle_breakfast INT DEFAULT NULL, lunch INT DEFAULT NULL, dinner INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE query DROP INDEX IDX_24BDB5EB9B6B5FBA, ADD UNIQUE INDEX UNIQ_24BDB5EB9B6B5FBA (account_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE meal_schedule');
        $this->addSql('ALTER TABLE query DROP INDEX UNIQ_24BDB5EB9B6B5FBA, ADD INDEX IDX_24BDB5EB9B6B5FBA (account_id)');
    }
}
