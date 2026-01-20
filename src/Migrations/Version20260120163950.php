<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260120163950 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'Create webgriffe_sylius_heylight_webhook_token table if not exists';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        if ($schema->hasTable('webgriffe_sylius_heylight_webhook_token')) {
            return;
        }
        $this->addSql('CREATE TABLE webgriffe_sylius_heylight_webhook_token (id INT AUTO_INCREMENT NOT NULL, payment_id INT NOT NULL, token VARCHAR(255) NOT NULL, UNIQUE INDEX payment_idx (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webgriffe_sylius_heylight_webhook_token ADD CONSTRAINT FK_EA20794D4C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id) ON DELETE CASCADE');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE webgriffe_sylius_heylight_webhook_token DROP FOREIGN KEY FK_EA20794D4C3A3BB');
        $this->addSql('DROP TABLE webgriffe_sylius_heylight_webhook_token');
    }
}
