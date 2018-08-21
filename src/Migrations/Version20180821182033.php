<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180821182033 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meal_schedule ADD restaurant INT NOT NULL, ADD breakfast_time TIME DEFAULT NULL, ADD middle_breakfast_time TIME DEFAULT NULL, ADD lunch_time TIME DEFAULT NULL, ADD dinner_time TIME DEFAULT NULL, DROP breakfast, DROP middle_breakfast, DROP lunch, DROP dinner');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meal_schedule ADD breakfast INT DEFAULT NULL, ADD middle_breakfast INT DEFAULT NULL, ADD lunch INT DEFAULT NULL, ADD dinner INT DEFAULT NULL, DROP restaurant, DROP breakfast_time, DROP middle_breakfast_time, DROP lunch_time, DROP dinner_time');
    }
}
