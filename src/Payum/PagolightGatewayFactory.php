<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum;

use ArrayObject;
use Payum\Core\Bridge\Spl\ArrayObject as PayumArrayObject;
use Payum\Core\GatewayFactory;

final class PagolightGatewayFactory extends GatewayFactory
{
    protected function populateConfig(PayumArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'pagolight',
            'payum.factory_title' => 'Pagolight',
            'payum.action.status' => '@webgriffe_sylius_pagolight.payum.action.status',
        ]);

        if (false === (bool) $config['payum.api']) {
            $defaultOptions = ['sandbox' => true];
            $config->defaults($defaultOptions);
            $config['payum.default_options'] = $defaultOptions;
            $config['payum.required_options'] = ['merchant_key', 'sandbox'];

            /**
             * @psalm-suppress MixedArgumentTypeCoercion
             *
             * @phpstan-ignore-next-line
             */
            $config['payum.api'] = static fn (ArrayObject $config): PagolightApi => new PagolightApi((array) $config);
        }
    }
}
