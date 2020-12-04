<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Test\Serializer;

use MagicLegacy\Component\MtgMelee\Entity\Pairing;
use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeSerializerException;
use MagicLegacy\Component\MtgMelee\Serializer\MtgMeleeSerializer;
use PHPUnit\Framework\TestCase;
use Safe;
use Safe\Exceptions\JsonException;

/**
 * Class SerializerTest
 *
 * @author Romain Cottard
 */
class SerializerTest extends TestCase
{
    /**
     * @return void
     * @throws MtgMeleeSerializerException
     */
    public function testICanSerializeAndDeserializeAValueObject(): void
    {
        //~ Serializer / Unserializer service
        $serializer = new MtgMeleeSerializer();

        //~ Create new original VO
        $originalEntity = new Pairing(1, 2);

        //~ Serialize VO
        $json = $serializer->serialize($originalEntity);

        //~ Unserialize VO
        /** @var Pairing $unserializedEntity */
        $unserializedEntity = $serializer->unserialize($json, Pairing::class);

        //~ Compare data
        $this->assertEquals($originalEntity, $unserializedEntity);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function testAnExceptionIsThrownWhenUnserializedStringHaveNotSupportedField(): void
    {
        $data = ['tournamentId' => 1, 'anyField' => 'any'];

        $this->expectException(MtgMeleeSerializerException::class);
        (new MtgMeleeSerializer())->unserialize(Safe\json_encode($data), Pairing::class);
    }

    /**
     * @return void
     */
    public function testAnMtgMeleeSerializerExceptionIsThrownWhenSerializeInvalidData(): void
    {
        $this->expectException(MtgMeleeSerializerException::class);
        (new MtgMeleeSerializer())->serialize(
            new class implements \JsonSerializable {
                public function jsonSerialize()
                {
                    return "\xB1\x31";
                }
            }
        );
    }

    /**
     * @return void
     */
    public function testAnMtgMeleeSerializerExceptionIsThrownWhenUnserializeInvalidJson(): void
    {
        $this->expectException(MtgMeleeSerializerException::class);
        (new MtgMeleeSerializer())->unserialize('[', Pairing::class);
    }

    /**
     * @return void
     */
    public function testAnMtgMeleeSerializerExceptionIsThrownWhenUnserializeWithNonExistingClass(): void
    {
        $this->expectException(MtgMeleeSerializerException::class);
        (new MtgMeleeSerializer())->unserialize('[]', 'Test\Hello\Not\Exists');
    }
}
