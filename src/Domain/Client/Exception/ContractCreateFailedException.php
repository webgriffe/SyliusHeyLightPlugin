<?php

declare(strict_types=1);

namespace Webgriffe\SyliusPagolightPlugin\Domain\Client\Exception;

use RuntimeException;

final class ContractCreateFailedException extends RuntimeException implements ExceptionInterface
{
}
