<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Form\Type\SyliusPagolightGatewayConfigurationType;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.form.type.gateway_configuration', SyliusPagolightGatewayConfigurationType::class)
        ->tag('sylius.gateway_configuration_type', ['type' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'label' => 'Pagolight'])
        ->tag('sylius.gateway_configuration_type', ['type' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'label' => 'Pagolight Pro'])
        ->tag('form.type')
    ;
};
