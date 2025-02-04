<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\Ui;

if (!interface_exists(\Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Repository\RepositoryInterface::class, \Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class);
}
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Tests\Webgriffe\SyliusHeylightPlugin\Behat\Context\PayumPaymentTrait;
use Tests\Webgriffe\SyliusHeylightPlugin\Behat\Page\Shop\Payment\ProcessPageInterface;
use Webmozart\Assert\Assert;

final class HeylightContext implements Context
{
    use PayumPaymentTrait;

    public function __construct(
        private readonly RepositoryInterface $paymentTokenRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Session $session,
        private readonly ProcessPageInterface $paymentProcessPage,
        private readonly ThankYouPageInterface $thankYouPage,
        private readonly ShowPageInterface $orderShowPage,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
        // TODO: Why config parameters are not loaded?
        $this->urlGenerator->setContext(new RequestContext('', 'GET', '127.0.0.1:8080', 'https'));
    }

    /**
     * @When I complete the payment on Heylight
     */
    public function iCompleteThePaymentOnHeylight(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        // Simulate coming back from Heylight after completed checkout
        $this->session->getDriver()->visit($paymentCaptureSecurityToken->getTargetUrl());
    }

    /**
     * @Given I have cancelled Heylight payment
     * @When I cancel the payment on Heylight
     */
    public function iCancelThePaymentOnHeylight(): void
    {
        $payment = $this->getCurrentPayment();
        [$paymentCaptureSecurityToken, $paymentNotifySecurityToken, $paymentCancelSecurityToken] = $this->getCurrentPaymentSecurityTokens($payment);

        // Simulate coming back from Heylight after clicking on cancel link
        $this->session->getDriver()->visit($paymentCancelSecurityToken->getTargetUrl());
    }

    /**
     * @Then I should be on the waiting payment processing page
     */
    public function iShouldBeOnTheWaitingPaymentProcessingPage(): void
    {
        $payment = $this->getCurrentPayment();
        $this->paymentProcessPage->verify([
            'tokenValue' => $payment->getOrder()?->getTokenValue(),
        ]);
    }

    /**
     * @Then /^I should be redirected to the thank you page$/
     */
    public function iShouldBeRedirectedToTheThankYouPage(): void
    {
        $this->paymentProcessPage->waitForRedirect();
        Assert::true($this->thankYouPage->hasThankYouMessage());
    }

    /**
     * @When I try to pay again with Heylight
     */
    public function iTryToPayAgainWithHeylight(): void
    {
        $this->orderShowPage->pay();
        $this->iCompleteThePaymentOnHeylight();
    }

    /**
     * @Then /^I should be redirected to the order page/
     */
    public function iShouldBeRedirectedToTheOrderPage(): void
    {
        $this->paymentProcessPage->waitForRedirect();
        $orders = $this->orderRepository->findAll();
        $order = reset($orders);
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::true($this->orderShowPage->isOpen(['tokenValue' => $order->getTokenValue()]));
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
}
