<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906215316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, hotel_id INT DEFAULT NULL, tarif DOUBLE PRECISION NOT NULL, superficie DOUBLE PRECISION NOT NULL, vue_sur_mer INT NOT NULL, chaine_ala_carte INT NOT NULL, climatisation INT NOT NULL, television_ecran_plat INT NOT NULL, telephone INT NOT NULL, chaine_satellite INT NOT NULL, chaine_du_cable INT NOT NULL, coffre_fort INT NOT NULL, materiel_de_repassage INT NOT NULL, wifi_gratuit INT NOT NULL, type INT NOT NULL, UNIQUE INDEX UNIQ_C509E4FF3243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, nb_chambre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reserver (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, chambre_id INT DEFAULT NULL, prix FLOAT NOT NULL, date_entree DATE NOT NULL, date_sortie DATE NOT NULL, Validite INT NOT NULL, INDEX IDX_B9765E93A76ED395 (user_id), INDEX IDX_B9765E939B177F54 (chambre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE reserver ADD CONSTRAINT FK_B9765E93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reserver ADD CONSTRAINT FK_B9765E939B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF3243BB18');
        $this->addSql('ALTER TABLE reserver DROP FOREIGN KEY FK_B9765E93A76ED395');
        $this->addSql('ALTER TABLE reserver DROP FOREIGN KEY FK_B9765E939B177F54');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE reserver');
        $this->addSql('DROP TABLE user');
    }
}
