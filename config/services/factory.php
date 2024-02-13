<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Converter\ContractConverter;
use Webgriffe\SyliusPagolightPlugin\Converter\ContractConverterInterface;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookToken;
use Webgriffe\SyliusPagolightPlugin\Factory\WebhookTokenFactory;
use Webgriffe\SyliusPagolightPlugin\Factory\WebhookTokenFactoryInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $containerConfigurator->parameters()
        ->set('webgriffe_sylius_pagolight.webhook_token.class', WebhookToken::class)
    ;

    $services->set('webgriffe_sylius_pagolight.factory.webhook_token', WebhookTokenFactory::class)
        ->args([
            param('webgriffe_sylius_pagolight.webhook_token.class'),
        ])
    ;

    $services->alias(WebhookTokenFactoryInterface::class, 'webgriffe_sylius_pagolight.factory.webhook_token');
};
