<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('webgriffe_sylius_heylight_plugin_payment_process', '/order/{tokenValue}/payment/heylight-process')
        ->controller(['webgriffe_sylius_heylight.controller.payment', 'processAction'])
        ->methods(['GET'])
    ;
};
