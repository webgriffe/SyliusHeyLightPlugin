<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SyliusHeylightGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('merchant_key', TextType::class, [
                'label' => 'webgriffe_sylius_heylight.form.gateway_configuration.merchant_key',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('sandbox', CheckboxType::class, [
                'label' => 'webgriffe_sylius_heylight.form.gateway_configuration.sandbox',
                'required' => false,
            ])
            ->add('allowed_terms', ChoiceType::class, [
                'label' => 'webgriffe_sylius_heylight.form.gateway_configuration.allowed_terms',
                'help' => 'webgriffe_sylius_heylight.form.gateway_configuration.allowed_terms_help',
                'choices' => array_combine(range(1, 24), range(1, 24)),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
        ;
    }
}
