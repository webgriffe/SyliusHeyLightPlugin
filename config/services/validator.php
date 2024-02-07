<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusPagolightPlugin\Validator\PagolightPaymentMethodUniqueValidator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_pagolight.validator.pagolight_payment_method_unique', PagolightPaymentMethodUniqueValidator::class)
        ->args([
            service('sylius.repository.payment_method'),
        ])
        ->tag('validator.constraint_validator')
    ;
};
