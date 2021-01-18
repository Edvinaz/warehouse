<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118093258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE people_details (id INT AUTO_INCREMENT NOT NULL, people_id INT NOT NULL, date DATE NOT NULL, expires DATE DEFAULT NULL, discr VARCHAR(16) NOT NULL, result VARCHAR(255) DEFAULT NULL, INDEX IDX_328F17CE3147C936 (people_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE people_details ADD CONSTRAINT FK_328F17CE3147C936 FOREIGN KEY (people_id) REFERENCES people (id)');
        $this->addSql('ALTER TABLE fuel_received2019 ADD CONSTRAINT FK_173A6DF09909C13F FOREIGN KEY (transport_id) REFERENCES transport (id)');
        $this->addSql('ALTER TABLE staff CHANGE discr discr VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE ware_objects DROP discr');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE people_details');
        $this->addSql('ALTER TABLE fuel_received2019 DROP FOREIGN KEY FK_173A6DF09909C13F');
        $this->addSql('ALTER TABLE staff CHANGE discr discr VARCHAR(4) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON `user`');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ware_objects ADD discr VARCHAR(8) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
