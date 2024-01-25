<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\Api\ApplicationStatusAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\Api\AuthAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\Api\CreateContractAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\CancelAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\CaptureAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\ConvertPaymentToContractAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\FailAction;
use Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum\Action\StatusAction;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.payum.action.capture', CaptureAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
        ])
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.capture'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.status', StatusAction::class)
        ->public()
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.convert_payment_to_contract', ConvertPaymentToContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.converter.contract'),
        ])
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.convert_payment_to_contract'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.cancel', CancelAction::class)
        ->public()
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.cancel'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.fail', FailAction::class)
        ->public()
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.fail'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.api.auth', AuthAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
            service('webgriffe_sylius_pagolight.cache'),
        ])
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.api.auth'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.api.create_contract', CreateContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
        ])
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.api.create_contract'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.api.application_status', ApplicationStatusAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
        ])
        ->tag('payum.action', ['factory' => 'pagolight', 'alias' => 'payum.action.api.application_status'])
    ;
};
