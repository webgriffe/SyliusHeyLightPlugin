<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception;

use RuntimeException;

final class AuthFailedException extends RuntimeException implements ExceptionInterface
{
}
