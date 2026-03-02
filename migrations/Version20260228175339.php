<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260228175339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            login VARCHAR(50) NOT NULL, 
            pass VARCHAR(255) NOT NULL, 
            phone VARCHAR(20) NOT NULL, 
            roles JSON NOT NULL, 
            api_token VARCHAR(255) DEFAULT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME NOT NULL, 
            UNIQUE INDEX login (login), 
            UNIQUE INDEX api_token (api_token), 
            PRIMARY KEY (id)) 
        DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
