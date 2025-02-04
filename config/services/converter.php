<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Converter\ContractConverter;
use Webgriffe\SyliusHeylightPlugin\Converter\ContractConverterInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.converter.contract', ContractConverter::class);

    $services->alias(ContractConverterInterface::class, 'webgriffe_sylius_heylight.converter.contract');
};
