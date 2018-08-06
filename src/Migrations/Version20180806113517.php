<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180806113517 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account CHANGE agent manager VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE query ADD account_id INT DEFAULT NULL, DROP name, DROP manager, DROP sport, DROP country');
        $this->addSql('ALTER TABLE query ADD CONSTRAINT FK_24BDB5EB9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_24BDB5EB9B6B5FBA ON query (account_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account CHANGE manager agent VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE query DROP FOREIGN KEY FK_24BDB5EB9B6B5FBA');
        $this->addSql('DROP INDEX IDX_24BDB5EB9B6B5FBA ON query');
        $this->addSql('ALTER TABLE query ADD name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD manager VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD sport VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD country VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP account_id');
    }
}
