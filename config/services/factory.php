<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Converter\ContractConverter;
use Webgriffe\SyliusHeylightPlugin\Converter\ContractConverterInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookToken;
use Webgriffe\SyliusHeylightPlugin\Factory\WebhookTokenFactory;
use Webgriffe\SyliusHeylightPlugin\Factory\WebhookTokenFactoryInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $containerConfigurator->parameters()
        ->set('webgriffe_sylius_heylight.webhook_token.class', WebhookToken::class)
    ;

    $services->set('webgriffe_sylius_heylight.factory.webhook_token', WebhookTokenFactory::class)
        ->args([
            param('webgriffe_sylius_heylight.webhook_token.class'),
        ])
    ;

    $services->alias(WebhookTokenFactoryInterface::class, 'webgriffe_sylius_heylight.factory.webhook_token');
};
