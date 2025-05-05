<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427133345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP CONSTRAINT fk_c53d045fccfa12b8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_c53d045fccfa12b8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP profile_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649c4cf44dc
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_8d93d649c4cf44dc
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP profile_image_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD profile_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD CONSTRAINT fk_c53d045fccfa12b8 FOREIGN KEY (profile_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_c53d045fccfa12b8 ON image (profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD profile_image_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649c4cf44dc FOREIGN KEY (profile_image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_8d93d649c4cf44dc ON "user" (profile_image_id)
        SQL);
    }
}
