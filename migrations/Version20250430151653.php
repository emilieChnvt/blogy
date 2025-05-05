<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430151653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP CONSTRAINT fk_c53d045fa76ed395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_c53d045fa76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD image_profile_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649A024058E FOREIGN KEY (image_profile_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649A024058E ON "user" (image_profile_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD CONSTRAINT fk_c53d045fa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_c53d045fa76ed395 ON image (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649A024058E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649A024058E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP image_profile_id
        SQL);
    }
}
