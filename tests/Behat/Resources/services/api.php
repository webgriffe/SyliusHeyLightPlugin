<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Api\PagolightContext;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_pagolight.behat.context.api.pagolight', PagolightContext::class)
        ->args([
            service('sylius.repository.payment_security_token'),
            service('sylius.repository.payment'),
            service('router'),
            service('sylius.http_client'),
            service('behat.mink.default_session'),
            service('sylius.behat.page.shop.order.show'),
            service('webgriffe_sylius_pagolight.behat.page.shop.payum.capture.do'),
        ])
    ;
};
