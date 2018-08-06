<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180806121999 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account convert to character SET utf8 collate utf8_unicode_ci');
        $this->addSql('ALTER TABLE facility convert to character SET utf8 collate utf8_unicode_ci');
        $this->addSql('ALTER TABLE query convert to character SET utf8 collate utf8_unicode_ci');
        $this->addSql('ALTER TABLE schedule convert to character SET utf8 collate utf8_unicode_ci');
        $this->addSql('ALTER TABLE user convert to character SET utf8 collate utf8_unicode_ci');

    }

    public function down(Schema $schema) : void
    {

    }
}
