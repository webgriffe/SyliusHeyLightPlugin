<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Ui\PagolightContext;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_pagolight.behat.context.ui.pagolight', PagolightContext::class)
        ->args([
            service('sylius.repository.payment_security_token'),
            service('sylius.repository.payment'),
            service('router'),
            service('behat.mink.default_session'),
            service('webgriffe_sylius_pagolight.behat.page.shop.payum.capture.do'),
            service('sylius.behat.page.shop.order.thank_you'),
            service('sylius.behat.page.shop.order.show'),
        ])
    ;
};
