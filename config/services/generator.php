<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Generator\WebhookTokenGenerator;
use Webgriffe\SyliusPagolightPlugin\Generator\WebhookTokenGeneratorInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.generator.webhook_token', WebhookTokenGenerator::class)
        ->args([
            service('webgriffe_sylius_pagolight.factory.webhook_token'),
            service('webgriffe_sylius_pagolight.repository.webhook_token'),
        ])
    ;

    $services->alias(WebhookTokenGeneratorInterface::class, 'webgriffe_sylius_pagolight.generator.webhook_token');
};
