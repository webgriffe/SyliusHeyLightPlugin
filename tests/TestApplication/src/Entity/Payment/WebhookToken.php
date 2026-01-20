<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusHeylightPlugin\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookToken as BaseWebhookToken;

#[ORM\Entity]
#[ORM\Table(name: 'webgriffe_sylius_heylight_webhook_token')]
class WebhookToken extends BaseWebhookToken
{
}
