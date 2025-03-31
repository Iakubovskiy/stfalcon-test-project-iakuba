<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331081517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD price_amount DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD price_currency_id VARCHAR(3) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD size_value DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD size_measurement VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD location_address VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD location_coordinates_latitude DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD location_coordinates_longitude DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP price
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP size
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP location
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP CONSTRAINT fk_8bf21cde3414710b;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER COLUMN agent_id DROP DEFAULT;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER agent_id TYPE UUID USING agent_id::uuid;
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.agent_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER COLUMN id DROP DEFAULT;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER id TYPE UUID  USING id::uuid
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD CONSTRAINT fk_8bf21cde3414710b FOREIGN KEY (agent_id) REFERENCES "user" (id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD price VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD size VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ADD location VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP price_amount
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP price_currency_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP size_value
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP size_measurement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP location_address
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP location_coordinates_latitude
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property DROP location_coordinates_longitude
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE property ALTER agent_id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN property.agent_id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER id TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".id IS NULL
        SQL);
    }
}
