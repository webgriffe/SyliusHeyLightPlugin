<?php

declare(strict_types=1);

namespace Webgriffe\SyliusHeylightPlugin;

use function dirname;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class WebgriffeSyliusHeylightPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
