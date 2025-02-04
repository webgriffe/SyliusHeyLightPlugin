<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Container\ContainerInterface;
use Webgriffe\SyliusHeylightPlugin\Controller\PaymentController;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.controller.payment', PaymentController::class)
        ->args([
            service('sylius.repository.order'),
            service('request_stack'),
            service('payum.security.token_storage'),
            service('router'),
            service('sylius.repository.payment'),
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
    ;
};
