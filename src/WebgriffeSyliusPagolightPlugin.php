<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin;

use function dirname;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webgriffe\SyliusPagolightPlugin\DependencyInjection\WebgriffeSyliusPagolightExtension;

final class WebgriffeSyliusPagolightPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    protected function getContainerExtensionClass(): string
    {
        return WebgriffeSyliusPagolightExtension::class;
    }
}
