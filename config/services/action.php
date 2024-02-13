<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Payum\Action\Api\AuthAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\Api\CreateContractAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\CancelAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\CaptureAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\ConvertPaymentToContractAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\NotifyAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\RemovePaymentWebhookTokenAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\RetrievePaymentWebhookTokenAction;
use Webgriffe\SyliusPagolightPlugin\Payum\Action\StatusAction;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.payum.action.capture', CaptureAction::class)
        ->public()
        ->args([
            service('twig'),
            service('router'),
            service('webgriffe_sylius_pagolight.generator.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.capture'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.capture'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.status', StatusAction::class)
        ->public()
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.cancel', CancelAction::class)
        ->public()
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.cancel'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.cancel'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.notify', NotifyAction::class)
        ->public()
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.notify'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.notify'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.convert_payment_to_contract', ConvertPaymentToContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.converter.contract'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.convert_payment_to_contract'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.convert_payment_to_contract'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.retrieve_payment_webhook_token', RetrievePaymentWebhookTokenAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.repository.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.retrieve_payment_webhook_token'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.retrieve_payment_webhook_token'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.remove_payment_webhook_token', RemovePaymentWebhookTokenAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.repository.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.remove_payment_webhook_token'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.remove_payment_webhook_token'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.api.auth', AuthAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
            service('webgriffe_sylius_pagolight.cache'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.api.auth'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.api.auth'])
    ;

    $services->set('webgriffe_sylius_pagolight.payum.action.api.create_contract', CreateContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_pagolight.client'),
        ])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_GATEWAY_CODE, 'alias' => 'payum.action.api.create_contract'])
        ->tag('payum.action', ['factory' => PagolightApi::PAGOLIGHT_PRO_GATEWAY_CODE, 'alias' => 'payum.action.api.create_contract'])
    ;
};
