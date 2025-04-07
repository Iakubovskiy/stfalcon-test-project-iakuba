<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407141930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE file (id UUID NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN file.id IS '(DC2Type:uuid)'
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE currency ALTER id TYPE UUID USING gen_random_uuid()
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN currency.id IS '(DC2Type:uuid)'
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER price_currency_id TYPE UUID USING gen_random_uuid()
        SQL);

        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.price_currency_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX UNIQ_8BF21CDE3FFDCD60 ON property (price_currency_id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT FK_8BF21CDE6BF700BD;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property_status ALTER id TYPE UUID USING gen_random_uuid()
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property_status.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER COLUMN status_id DROP DEFAULT;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER status_id TYPE UUID USING gen_random_uuid()
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE6BF700BD FOREIGN KEY (status_id) REFERENCES property_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.status_id IS '(DC2Type:uuid)'
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT FK_8BF21CDEC54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property_type ALTER id TYPE UUID USING id::uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER COLUMN type_id DROP DEFAULT;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER type_id TYPE UUID USING gen_random_uuid()
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.type_id IS '(DC2Type:uuid)'
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
            DROP TABLE file
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property_type ALTER id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property_type.id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property_status ALTER id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property_status.id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8BF21CDE3FFDCD60
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER status_id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER type_id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER photo_urls DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER price_currency_id TYPE VARCHAR(3)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.status_id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.type_id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.price_currency_id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE currency ALTER id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN currency.id IS NULL
        SQL);
    }
}
