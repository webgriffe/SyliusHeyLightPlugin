<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Doctrine\ORM\WebhookTokenRepository;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.repository.webhook_token', WebhookTokenRepository::class)
        ->args([
            '@doctrine.orm.default_entity_manager',
        ])
        ->tag('doctrine.repository_service')
    ;
};
