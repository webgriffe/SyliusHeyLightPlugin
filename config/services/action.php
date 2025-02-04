<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Payum\Action\Api\AuthAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\Api\CreateContractAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\CancelAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\CaptureAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\ConvertPaymentToContractAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\NotifyAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\RemovePaymentWebhookTokenAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\RetrievePaymentWebhookTokenAction;
use Webgriffe\SyliusHeylightPlugin\Payum\Action\StatusAction;
use Webgriffe\SyliusHeylightPlugin\Payum\HeylightApi;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.payum.action.capture', CaptureAction::class)
        ->public()
        ->args([
            service('router'),
            service('webgriffe_sylius_heylight.generator.webhook_token'),
            service('webgriffe_sylius_heylight.logger'),
            service('request_stack'),
            service('webgriffe_sylius_heylight.repository.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.capture'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.capture'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.status', StatusAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.logger'),
        ])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.cancel', CancelAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.logger'),
            service('request_stack'),
            service('router'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.cancel'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.cancel'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.notify', NotifyAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.logger'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.notify'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.notify'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.convert_payment_to_contract', ConvertPaymentToContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.converter.contract'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.convert_payment_to_contract'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.convert_payment_to_contract'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.retrieve_payment_webhook_token', RetrievePaymentWebhookTokenAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.repository.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.retrieve_payment_webhook_token'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.retrieve_payment_webhook_token'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.remove_payment_webhook_token', RemovePaymentWebhookTokenAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.repository.webhook_token'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.remove_payment_webhook_token'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.remove_payment_webhook_token'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.api.auth', AuthAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.client'),
            service('webgriffe_sylius_heylight.cache'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.api.auth'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.api.auth'])
    ;

    $services->set('webgriffe_sylius_heylight.payum.action.api.create_contract', CreateContractAction::class)
        ->public()
        ->args([
            service('webgriffe_sylius_heylight.client'),
        ])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE, 'alias' => 'payum.action.api.create_contract'])
        ->tag('payum.action', ['factory' => HeylightApi::HEYLIGHT_FINANCING_GATEWAY_CODE, 'alias' => 'payum.action.api.create_contract'])
    ;
};
