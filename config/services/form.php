<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Form\Type\SyliusHeylightGatewayConfigurationType;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.form.type.gateway_configuration', SyliusHeylightGatewayConfigurationType::class)
        ->tag('sylius.gateway_configuration_type', ['type' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'label' => 'HeyLight BNPL (0%)'])
        ->tag('sylius.gateway_configuration_type', ['type' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'label' => 'Heylight Financing'])
        ->tag('form.type')
    ;
};
