<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('webgriffe_sylius_pagolight_plugin_payment_process', '/order/{tokenValue}/payment/pagolight-process')
        ->controller(['webgriffe_sylius_pagolight.controller.payment', 'processAction'])
        ->methods(['GET'])
    ;
};
