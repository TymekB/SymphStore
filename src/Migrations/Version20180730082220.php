<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180730082220 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_ordered_product (order_id INT NOT NULL, ordered_product_id INT NOT NULL, INDEX IDX_2DF954EA8D9F6D38 (order_id), INDEX IDX_2DF954EA242FA9B5 (ordered_product_id), PRIMARY KEY(order_id, ordered_product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_ordered_product ADD CONSTRAINT FK_2DF954EA8D9F6D38 FOREIGN KEY (order_id) REFERENCES order_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_ordered_product ADD CONSTRAINT FK_2DF954EA242FA9B5 FOREIGN KEY (ordered_product_id) REFERENCES ordered_product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE order_ordered_product');
    }
}
