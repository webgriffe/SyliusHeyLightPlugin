<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\PayumPaymentTrait;
use Webgriffe\SyliusHeylightPlugin\Client\PaymentState;
use Webgriffe\SyliusHeylightPlugin\Entity\WebhookTokenInterface;
use Webgriffe\SyliusHeylightPlugin\Repository\WebhookTokenRepositoryInterface;
use Webmozart\Assert\Assert;

final class HeylightContext implements Context
{
    use PayumPaymentTrait;

    /**
     * @param RepositoryInterface<PaymentSecurityTokenInterface> $paymentTokenRepository
     * @param PaymentRepositoryInterface<PaymentInterface> $paymentRepository
     */
    public function __construct(
        private readonly RepositoryInterface $paymentTokenRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ClientInterface|Client $client,
        private readonly WebhookTokenRepositoryInterface $webhookTokenRepository,
    ) {
        // TODO: Why config parameters are not loaded?
        $this->urlGenerator->setContext(new RequestContext('', 'GET', '127.0.0.1:8080', 'https'));
    }

    /**
     * @When Heylight notify the store about the successful payment
     */
    public function heylightNotifyTheStoreAboutTheSuccessfulPayment(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken, $paymentNotifySecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);
        $webhookToken = $this->webhookTokenRepository->findOneByPayment($payment);
        Assert::isInstanceOf($webhookToken, WebhookTokenInterface::class);

        $this->notifyPaymentState($paymentNotifySecurityToken, [
            'status' => PaymentState::SUCCESS,
            'token' => $webhookToken->getToken(),
        ]);
    }

    /**
     * @When /^Heylight notify the store about the failed payment$/
     */
    public function heylightNotifyTheStoreAboutTheFailedPayment(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken, $paymentNotifySecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);
        $webhookToken = $this->webhookTokenRepository->findOneByPayment($payment);
        Assert::isInstanceOf($webhookToken, WebhookTokenInterface::class);

        $this->notifyPaymentState($paymentNotifySecurityToken, [
            'status' => PaymentState::CANCELLED,
            'token' => $webhookToken->getToken(),
        ]);
    }

    /**
     * @return PaymentRepositoryInterface<PaymentInterface>
     */
    protected function getPaymentRepository(): PaymentRepositoryInterface
    {
        return $this->paymentRepository;
    }

    /**
     * @return RepositoryInterface<PaymentSecurityTokenInterface>
     */
    protected function getPaymentTokenRepository(): RepositoryInterface
    {
        return $this->paymentTokenRepository;
    }

    private function notifyPaymentState(PaymentSecurityTokenInterface $token, array $responsePayload): void
    {
        $formParams = http_build_query($responsePayload);
        $request = new Request(
            'POST',
            $this->getNotifyUrl($token),
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            $formParams,
        );
        if ($this->client instanceof Client) {
            $this->client->send($request);

            return;
        }
        $this->client->sendRequest($request);
    }

    private function getNotifyUrl(PaymentSecurityTokenInterface $token): string
    {
        return $this->urlGenerator->generate(
            'payum_notify_do',
            ['payum_token' => $token->getHash()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
