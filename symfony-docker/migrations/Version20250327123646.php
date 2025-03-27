<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327123646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE currency (id VARCHAR(255) NOT NULL, name VARCHAR(5) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE property (id UUID NOT NULL, agent_id VARCHAR(255) DEFAULT NULL, status_id VARCHAR(255) DEFAULT NULL, type_id VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8BF21CDE3414710B ON property (agent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8BF21CDE6BF700BD ON property (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8BF21CDEC54C8C93 ON property (type_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE property_status (id VARCHAR(255) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE property_type (id VARCHAR(255) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_blocked BOOLEAN NOT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3414710B FOREIGN KEY (agent_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE6BF700BD FOREIGN KEY (status_id) REFERENCES property_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEC54C8C93 FOREIGN KEY (type_id) REFERENCES property_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT FK_8BF21CDE3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT FK_8BF21CDE6BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT FK_8BF21CDEC54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE currency
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE property
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE property_status
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE property_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
