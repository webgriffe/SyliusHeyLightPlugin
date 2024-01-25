<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Controller;

use Payum\Core\Payum;
use Payum\Core\Request\Cancel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Webgriffe\SyliusPagolightPlugin\Payum\Request\Fail;

final class PagolightController extends AbstractController
{
    public function __construct(
        private readonly Payum $payum,
    ) {
    }

    public function cancelAction(Request $request): RedirectResponse
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());
        $gateway->execute(new Cancel($token->getDetails()));

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        return $this->redirect($token->getAfterUrl());
    }

    public function failAction(Request $request): RedirectResponse
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());
        $gateway->execute(new Fail($token->getDetails()));

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        return $this->redirect($token->getAfterUrl());
    }
}
