<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Payum;

use ArrayObject;
use Payum\Core\Bridge\Spl\ArrayObject as PayumArrayObject;
use Payum\Core\GatewayFactory;

final class HeylightBnplGatewayFactory extends GatewayFactory
{
    #[\Override]
    protected function populateConfig(PayumArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => HeylightApi::HEYLIGHT_BNPL_GATEWAY_CODE,
            'payum.factory_title' => 'HeyLight BNPL (0%)',
            'payum.action.status' => '@webgriffe_sylius_heylight.payum.action.status',
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
            $config['payum.api'] = static fn (ArrayObject $config): HeylightApi => new HeylightApi((array) $config);
        }
    }
}
