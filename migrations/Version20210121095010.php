<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210121095010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F86223F98F');
        $this->addSql('DROP INDEX IDX_10C31F86223F98F ON rdv');
        $this->addSql('ALTER TABLE rdv ADD praticien_id INT NOT NULL, DROP praticien, CHANGE lastname_id patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F866B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F862391866B FOREIGN KEY (praticien_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_10C31F866B899279 ON rdv (patient_id)');
        $this->addSql('CREATE INDEX IDX_10C31F862391866B ON rdv (praticien_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F866B899279');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F862391866B');
        $this->addSql('DROP INDEX IDX_10C31F866B899279 ON rdv');
        $this->addSql('DROP INDEX IDX_10C31F862391866B ON rdv');
        $this->addSql('ALTER TABLE rdv ADD lastname_id INT NOT NULL, ADD praticien VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP patient_id, DROP praticien_id');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86223F98F FOREIGN KEY (lastname_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_10C31F86223F98F ON rdv (lastname_id)');
    }
}
