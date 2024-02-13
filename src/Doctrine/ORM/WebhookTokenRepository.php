<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookToken;
use Webgriffe\SyliusPagolightPlugin\Entity\WebhookTokenInterface;
use Webgriffe\SyliusPagolightPlugin\Repository\WebhookTokenRepositoryInterface;

/**
 * @extends ServiceEntityRepository<WebhookTokenInterface>
 */
final class WebhookTokenRepository extends ServiceEntityRepository implements WebhookTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebhookToken::class);
    }
}
