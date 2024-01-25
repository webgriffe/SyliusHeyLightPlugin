<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class PagolightProGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'pagolight_pro',
            'payum.factory_title' => 'Pagolight Pro',
            'payum.action.status' => '@webgriffe_sylius_pagolight.payum.action.status',
        ]);

        if (false === (bool) $config['payum.api']) {
            $defaultOptions = ['sandbox' => true];
            $config->defaults($defaultOptions);
            $config['payum.default_options'] = $defaultOptions;
            $config['payum.required_options'] = ['merchant_key', 'sandbox'];

            $config['payum.api'] = static fn (\ArrayObject $config): PagolightApi => new PagolightApi((array) $config);
        }
    }
}
