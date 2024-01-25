<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Infrastructure\Symfony\Controller\PagolightController;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.controller.return_from_external_website', PagolightController::class)
        ->args([
            service('payum'),
        ])
        ->tag('controller.service_arguments')
    ;
};
