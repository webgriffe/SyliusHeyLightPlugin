<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Webgriffe\SyliusPagolightPlugin\Controller\PagolightController;

return static function (RoutingConfigurator $routes): void {

    $routes->add('webgriffe_sylius_pagolight_cancel_payment', '/pagolight/cancel/{payum_token}')
        ->controller([PagolightController::class, 'cancelAction'])
    ;

    $routes->add('webgriffe_sylius_pagolight_fail_payment', '/pagolight/fail/{payum_token}')
        ->controller([PagolightController::class, 'failAction'])
    ;
};
