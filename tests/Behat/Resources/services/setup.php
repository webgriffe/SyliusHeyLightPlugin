<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\Setup\PaymentContext;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_heylight.behat.context.setup.payment', PaymentContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.payment_method'),
            service('sylius.fixture.example_factory.payment_method'),
            service('sylius.manager.payment_method'),
            [
                HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE => 'Heylight',
                HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE => 'Heylight PRO',
            ],
        ])
    ;
};
