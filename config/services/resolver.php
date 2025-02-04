<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Resolver\HeylightPaymentMethodsResolver;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.payment_methods_resolver.heylight', HeylightPaymentMethodsResolver::class)
        ->args([
            service('sylius.repository.payment_method'),
        ])
        ->tag('sylius.payment_method_resolver', [
            'type' => 'heylight',
            'label' => 'Heylight',
            'priority' => 2,
        ])
    ;
};
