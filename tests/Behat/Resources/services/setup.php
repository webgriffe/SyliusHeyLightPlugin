<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Setup\PaymentContext;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_pagolight.behat.context.setup.payment', PaymentContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.payment_method'),
            service('sylius.fixture.example_factory.payment_method'),
            service('sylius.manager.payment_method'),
            [
                PagolightApi::PAGOLIGHT_GATEWAY_CODE => 'Pagolight',
                PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE => 'Pagolight PRO',
            ],
        ])
    ;
};
