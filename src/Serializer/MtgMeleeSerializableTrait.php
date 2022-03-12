<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Serializer;

/**
 * Trait MtgMeleeSerializableTrait
 *
 * @author Romain Cottard
 */
trait MtgMeleeSerializableTrait
{
    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [];
        /** @var iterable $object */
        $object = $this;
        foreach ($object as $property => $value) {
            $data[$property] = ($value instanceof \JsonSerializable) ? $value->jsonSerialize() : $value;
        }

        return $data;
    }
}
