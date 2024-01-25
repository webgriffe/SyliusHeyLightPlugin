<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Domain\Converter\ContractConverter;
use Webgriffe\SyliusPagolightPlugin\Domain\Converter\ContractConverterInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.converter.contract', ContractConverter::class)
        ->args([
            service('payum'),
        ])
    ;

    $services->alias(ContractConverterInterface::class, 'webgriffe_sylius_pagolight.converter.contract');
};
