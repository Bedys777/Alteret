<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217163424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D8793C912');
        $this->addSql('CREATE TABLE alteret (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix INT NOT NULL, photo VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE lunette');
        $this->addSql('DROP INDEX IDX_6EEAA67D8793C912 ON commande');
        $this->addSql('ALTER TABLE commande CHANGE lunette_id alteret_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFC09F6BE FOREIGN KEY (alteret_id) REFERENCES alteret (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DFC09F6BE ON commande (alteret_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFC09F6BE');
        $this->addSql('CREATE TABLE lunette (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix INT NOT NULL, photo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, modele VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE alteret');
        $this->addSql('DROP INDEX IDX_6EEAA67DFC09F6BE ON commande');
        $this->addSql('ALTER TABLE commande CHANGE alteret_id lunette_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D8793C912 FOREIGN KEY (lunette_id) REFERENCES lunette (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6EEAA67D8793C912 ON commande (lunette_id)');
    }
}
