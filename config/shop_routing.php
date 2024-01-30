<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@PayumBundle/Resources/config/routing/cancel.xml');

    $routes->add('webgriffe_sylius_pagolight_plugin.payment.status', '/payment/{paymentId}/status')
        ->controller(['webgriffe_sylius_pagolight.controller.payment', 'statusAction'])
        ->methods(['GET'])
        ->requirements(['paymentId' => '\d+'])
    ;
};
