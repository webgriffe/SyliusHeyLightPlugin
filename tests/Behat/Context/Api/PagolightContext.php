<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use GuzzleHttp\ClientInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\PayumPaymentTrait;
use Webgriffe\SyliusPagolightPlugin\Client\PaymentState;

final class PagolightContext implements Context
{
    use PayumPaymentTrait;

    /**
     * @param RepositoryInterface<PaymentSecurityTokenInterface> $paymentTokenRepository
     */
    public function __construct(
        private readonly RepositoryInterface $paymentTokenRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ClientInterface $client,
    ) {
        // TODO: Why config parameters are not loaded?
        $this->urlGenerator->setContext(new RequestContext('', 'GET', '127.0.0.1:8080', 'https'));
    }

    /**
     * @When Pagolight notify the store about the successful payment
     */
    public function pagolightNotifyTheStoreAboutTheSuccessfulPayment(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken, $paymentNotifySecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        $this->notifyPaymentState($paymentNotifySecurityToken, [
            'status' => PaymentState::SUCCESS,
            'token' => 'pagolight',
        ]);
    }

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
        $this->client->request(
            'POST',
            $this->getNotifyUrl($token),
            ['form_params' => $responsePayload],
        );
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
