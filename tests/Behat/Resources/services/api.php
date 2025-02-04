<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\Api\HeylightContext;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_heylight.behat.context.api.heylight', HeylightContext::class)
        ->args([
            service('sylius.repository.payment_security_token'),
            service('sylius.repository.payment'),
            service('router'),
            service('sylius.http_client'),
            service('webgriffe_sylius_heylight.repository.webhook_token'),
        ])
    ;
};
