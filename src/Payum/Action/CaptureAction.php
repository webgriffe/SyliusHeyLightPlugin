<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Payum\Action;

use GuzzleHttp\Client;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Webgriffe\SyliusPagolightPlugin\Payum\PagolightApi;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    private PagolightApi $api;

    public function __construct(
        private readonly Client $client
    ) {
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getModel();

        $token = $request->getToken();
        Assert::isInstanceOf($token, TokenInterface::class);

        $order = $payment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $response = $this->client->request(
            'POST',
            $this->api->getApiEndpoint() . '/auth/v1/generate/',
            [
                'body' => json_encode([
                    'merchant_key' => $this->api->getMerchantKey(),
                ]),
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        /** @var array{status: string, data: array{token: string}} $decodedResponse */
        $decodedResponse = json_decode($response->getBody()->getContents(), true);

        $bearerToken = $decodedResponse['data']['token'];

        $orderPayload = [
            'amount' => [
                'amount' => $order->getTotal(),
                'currency' => $order->getCurrencyCode(),
            ],
            'amount_format' => 'MINOR_UNIT',
            'billing_address' => [
                'address_line_1' => 'Via Roma 1',
                'country_code' => 'IT',
                'zip_code' => '42019',
                'city' => 'Arceto',
            ],
            'shipping_address' => [
                'address_line_1' => 'Via Roma 1',
                'country_code' => 'IT',
                'zip_code' => '42019',
                'city' => 'Arceto',
            ],
            'store_id' => $order->getChannel()->getCode(),
            'redirect_urls' => [
                'success_url' => $token->getTargetUrl(),
                'failure_url' => $token->getTargetUrl(),
                'cancel_url' => $token->getTargetUrl(),
            ]
        ];
        echo json_encode($orderPayload);
        die();

        $response = $this->client->request(
            'POST',
            $this->api->getApiEndpoint() . '/api/checkout/v1/init/',
            [
                'body' => json_encode($orderPayload, JSON_THROW_ON_ERROR),
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'authorization' => 'Bearer ' . $bearerToken,
                ],
            ],
        );

        /** @var array{action: string, redirect_url: string, external_contract_uuid: string} $decodedResponse */
        $decodedResponse = json_decode($response->getBody()->getContents(), true);

        throw new HttpPostRedirect(
            $decodedResponse['redirect_url'],
        );

    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    public function setApi($api): void
    {
        if (!$api instanceof PagolightApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . PagolightApi::class);
        }

        $this->api = $api;
    }
}
