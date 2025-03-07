<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Core\Model\PaymentInterface;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookToken;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;

/**
 * @extends ServiceEntityRepository<WebhookTokenInterface>
 */
final class WebhookTokenRepository extends ServiceEntityRepository implements WebhookTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebhookToken::class);
    }

    public function add(WebhookTokenInterface $webhookToken): void
    {
        $this->getEntityManager()->persist($webhookToken);
        $this->getEntityManager()->flush();
    }

    public function findOneByPayment(PaymentInterface $payment): ?WebhookTokenInterface
    {
        return $this->findOneBy(['payment' => $payment]);
    }

    public function remove(WebhookTokenInterface $webhookToken): void
    {
        $this->getEntityManager()->remove($webhookToken);
        $this->getEntityManager()->flush();
    }
}
