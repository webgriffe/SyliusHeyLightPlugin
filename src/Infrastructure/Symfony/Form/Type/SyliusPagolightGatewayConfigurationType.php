<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Infrastructure\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SyliusPagolightGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('merchant_key', TextType::class)
            ->add('sandbox', CheckboxType::class)
        ;
    }
}
