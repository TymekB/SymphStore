<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180730083804 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_product ADD product_id INT NOT NULL, DROP name, DROP description, DROP price, DROP slug, DROP img');
        $this->addSql('ALTER TABLE ordered_product ADD CONSTRAINT FK_E6F097B64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6F097B64584665A ON ordered_product (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ordered_product DROP FOREIGN KEY FK_E6F097B64584665A');
        $this->addSql('DROP INDEX UNIQ_E6F097B64584665A ON ordered_product');
        $this->addSql('ALTER TABLE ordered_product ADD name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, ADD price DOUBLE PRECISION NOT NULL, ADD slug VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD img VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP product_id');
    }
}
