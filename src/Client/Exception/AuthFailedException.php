<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Client\Exception;

use RuntimeException;

final class AuthFailedException extends RuntimeException implements ExceptionInterface
{
}
