<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusPagolightPlugin\App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookToken as BaseWebhookToken;

/**
 * @ORM\Entity
 * @ORM\Table(name="webgriffe_sylius_pagolight_webhook_token")
 */
class WebhookToken extends BaseWebhookToken
{
}
