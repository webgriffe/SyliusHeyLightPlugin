<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Doctrine\ORM\WebhookTokenRepository;
use Webgriffe\SyliusPagolightPlugin\Repository\WebhookTokenRepositoryInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.repository.webhook_token', WebhookTokenRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services->alias(WebhookTokenRepositoryInterface::class, 'webgriffe_sylius_pagolight.repository.webhook_token');
};
