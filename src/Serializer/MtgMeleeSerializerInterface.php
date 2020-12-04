<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Serializer;

use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeSerializerException;

/**
 * Interface JsonSerializerInterface
 *
 * @author Romain Cottard
 */
interface MtgMeleeSerializerInterface extends \JsonSerializable
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws MtgMeleeSerializerException
     */
    public function serialize(\JsonSerializable $object): string;

    /**
     * @param string $json
     * @return MtgMeleeSerializerInterface
     * @throws MtgMeleeSerializerException
     */
    public static function deserialize(string $json): MtgMeleeSerializerInterface;
}
