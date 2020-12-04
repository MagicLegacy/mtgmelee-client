<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Exception;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * Class MtgMeleeClientException
 *
 * @author Romain Cottard
 */
class MtgMeleeClientException extends MtgMeleeComponentException implements ClientExceptionInterface
{
}
