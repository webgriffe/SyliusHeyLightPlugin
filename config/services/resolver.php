<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Resolver\PagolightPaymentMethodsResolver;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.payment_methods_resolver.pagolight', PagolightPaymentMethodsResolver::class)
        ->args([
            service('sylius.repository.payment_method'),
        ])
        ->tag('sylius.payment_method_resolver', [
            'type' => 'pagolight',
            'label' => 'Pagolight',
            'priority' => 2,
        ])
    ;
};
