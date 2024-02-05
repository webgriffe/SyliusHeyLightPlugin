<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Page\Shop\Payum\Capture\PayumCaptureDoPage;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set('webgriffe_sylius_pagolight.behat.page.shop.payum.capture.do', PayumCaptureDoPage::class)
        ->parent('sylius.behat.symfony_page')
    ;
};
