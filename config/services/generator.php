<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Generator\WebhookTokenGenerator;
use Webgriffe\SyliusHeylightPlugin\Generator\WebhookTokenGeneratorInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.generator.webhook_token', WebhookTokenGenerator::class)
        ->args([
            service('webgriffe_sylius_heylight.factory.webhook_token'),
            service('webgriffe_sylius_heylight.repository.webhook_token'),
        ])
    ;

    $services->alias(WebhookTokenGeneratorInterface::class, 'webgriffe_sylius_heylight.generator.webhook_token');
};
