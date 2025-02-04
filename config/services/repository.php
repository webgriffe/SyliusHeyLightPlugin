<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusHeylightPlugin\Doctrine\ORM\WebhookTokenRepository;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_heylight.repository.webhook_token', WebhookTokenRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services->alias(WebhookTokenRepositoryInterface::class, 'webgriffe_sylius_heylight.repository.webhook_token');
};
