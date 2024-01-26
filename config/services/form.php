<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Form\Type\SyliusPagolightGatewayConfigurationType;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.form.type.gateway_configuration', SyliusPagolightGatewayConfigurationType::class)
        ->tag('sylius.gateway_configuration_type', ['type' => 'pagolight', 'label' => 'Pagolight'])
        ->tag('sylius.gateway_configuration_type', ['type' => 'pagolight_pro', 'label' => 'Pagolight Pro'])
        ->tag('form.type')
    ;
};
