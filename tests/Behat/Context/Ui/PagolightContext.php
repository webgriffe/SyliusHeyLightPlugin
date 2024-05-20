<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Context\PayumPaymentTrait;
use Tests\Webgriffe\SyliusPagolightPlugin\Behat\Page\Shop\Payum\Capture\PayumCaptureDoPageInterface;
use Webmozart\Assert\Assert;

final class PagolightContext implements Context
{
    use PayumPaymentTrait;

    /**
     * @param RepositoryInterface<PaymentSecurityTokenInterface> $paymentTokenRepository
     * @param PaymentRepositoryInterface<PaymentInterface> $paymentRepository
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private readonly RepositoryInterface $paymentTokenRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Session $session,
        private readonly PayumCaptureDoPageInterface $payumCaptureDoPage,
        private readonly ThankYouPageInterface $thankYouPage,
        private readonly ShowPageInterface $orderShowPage,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
        // TODO: Why config parameters are not loaded?
        $this->urlGenerator->setContext(new RequestContext('', 'GET', '127.0.0.1:8080', 'https'));
    }

    /**
     * @When I complete the payment on Pagolight
     */
    public function iCompleteThePaymentOnPagolight(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        // Simulate coming back from Pagolight after completed checkout
        $this->session->getDriver()->visit($paymentCaptureSecurityToken->getTargetUrl());
    }

    /**
     * @Given I have cancelled Pagolight payment
     * @When I cancel the payment on Pagolight
     */
    public function iCancelThePaymentOnPagolight(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken, $paymentNotifySecurityToken, $paymentCancelSecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        // Simulate coming back from Pagolight after clicking on cancel link
        $this->session->getDriver()->visit($paymentCancelSecurityToken->getTargetUrl());
    }

    /**
     * @Then I should be on the capture payment page
     */
    public function iShouldBeOnTheCapturePaymentPage(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        $this->payumCaptureDoPage->verify([
            'payum_token' => $paymentCaptureSecurityToken->getHash(),
        ]);
    }

    /**
     * @Then /^I should be redirected to the thank you page$/
     */
    public function iShouldBeRedirectedToTheThankYouPage(): void
    {
        $this->payumCaptureDoPage->waitForRedirect();
        Assert::true($this->thankYouPage->hasThankYouMessage());
    }

    /**
     * @When I try to pay again with Pagolight
     */
    public function iTryToPayAgainWithPagolight(): void
    {
        $this->orderShowPage->pay();
        $this->iCompleteThePaymentOnPagolight();
    }

    /**
     * @Then /^I should be redirected to the order page page$/
     */
    public function iShouldBeRedirectedToTheOrderPagePage(): void
    {
        $this->payumCaptureDoPage->waitForRedirect();
        $orders = $this->orderRepository->findAll();
        $order = reset($orders);
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::true($this->orderShowPage->isOpen(['tokenValue' => $order->getTokenValue()]));
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
}
