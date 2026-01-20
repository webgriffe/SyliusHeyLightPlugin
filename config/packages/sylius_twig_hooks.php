<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.payment_method.create.content.form.sections.gateway_configuration' => [
                'merchant_key' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/merchant_key.html.twig',
                    'priority' => 0,
                ],
                'allowed_terms' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/allowed_terms.html.twig',
                    'priority' => 0,
                ],
                'sandbox' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/sandbox.html.twig',
                    'priority' => 0,
                ],
            ],
            'sylius_admin.payment_method.update.content.form.sections.gateway_configuration' => [
                'merchant_key' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/merchant_key.html.twig',
                    'priority' => 0,
                ],
                'allowed_terms' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/allowed_terms.html.twig',
                    'priority' => 0,
                ],
                'sandbox' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/admin/payment_method/form/sandbox.html.twig',
                    'priority' => 0,
                ],
            ],
            'webgriffe_sylius_heylight.payment.process' => [
                'content' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/shop/payment/process/content.html.twig',
                    'priority' => 0,
                ],
            ],
            'webgriffe_sylius_heylight.payment.process.content' => [
                'content' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/shop/payment/process/content/loading.html.twig',
                    'priority' => 0,
                ],
            ],
            'webgriffe_sylius_heylight.payment.process#javascripts' => [
                'scripts' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/shop/payment/scripts.html.twig',
                    'priority' => 0,
                ],
            ],
            'sylius_shop.base.footer.content' => [
                'payment_methods' => [
                    'template' => '@WebgriffeSyliusHeylightPlugin/shop/shared/layout/base/footer/content/payment_methods.html.twig',
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
