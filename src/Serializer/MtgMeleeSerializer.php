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
 * Class MtgMeleeSerializer
 * Exception code range: 8200-8250
 * @author Romain Cottard
 */
final class MtgMeleeSerializer
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws MtgMeleeSerializerException
     */
    public function serialize(\JsonSerializable $object): string
    {
        try {
            return json_encode($object, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new MtgMeleeSerializerException('[CLI-8200] Cannot serialize data (json_encode failed)!', 8200, $exception);
        }
    }

    /**
     * @param string $json
     * @param string $class
     * @param bool $skippableParameters
     * @return MtgMeleeSerializerInterface|self
     * @throws MtgMeleeSerializerException
     */
    public function unserialize(string $json, string $class, bool $skippableParameters = false)
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            return $this->hydrate($class, $data, $skippableParameters);
        } catch (\JsonException $exception) {
            throw new MtgMeleeSerializerException('[CLI-8201] Cannot unserialize data (json_decode failed)!', 8201, $exception);
        }
    }

    /**
     * @param string $class
     * @param array $data
     * @param bool $skippableParameters
     * @return mixed
     * @throws MtgMeleeSerializerException
     */
    private function hydrate(string $class, array $data, bool $skippableParameters)
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $exception) {
            throw new MtgMeleeSerializerException("[CLI-8202] Given class does not exists! (class: '${class}')", 8203, $exception);
        }

        $parameters   = $reflection->getConstructor()->getParameters();
        $nbParameters = count($parameters);

        $orderedArguments = [];
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $argumentValue = null;

            if ($this->hasValidNamedData($parameterName, $data)) {
                $parameterReflectionClass = $parameter->getClass();
                $argumentValue            = $data[$parameterName];
                if ($this->isHydratableArgument($parameterReflectionClass, $argumentValue)) {
                    $argumentValue = $this->hydrate($parameterReflectionClass->getName(), $argumentValue, $skippableParameters);
                }
            } elseif ($this->hasValidArrayData($parameter, $nbParameters)) {
                $argumentValue = $data; // @codeCoverageIgnore
            } elseif (!$skippableParameters) {
                throw new MtgMeleeSerializerException("[CLI-8203] Cannot deserialize object: data '${parameterName}' does not exist!", 8203);
            }

            $orderedArguments[$parameter->getPosition()] = $argumentValue;
        }

        ksort($orderedArguments);

        return new $class(...$orderedArguments);
    }

    /**
     * @param string $parameterName
     * @param array $data
     * @return bool
     */
    private function hasValidNamedData(string $parameterName, array $data): bool
    {
        return array_key_exists($parameterName, $data);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param int $nbParameters
     * @return bool
     */
    private function hasValidArrayData(\ReflectionParameter $parameter, int $nbParameters): bool
    {
        return ($nbParameters === 1 && $parameter->isArray());
    }

    /**
     * @param \ReflectionClass|null $parameterReflectionClass
     * @param mixed $data
     * @return bool
     */
    private function isHydratableArgument(?\ReflectionClass $parameterReflectionClass, $data): bool
    {
        return (
            $parameterReflectionClass !== null
            && in_array(\JsonSerializable::class, $parameterReflectionClass->getInterfaceNames())
            && is_array($data)
        );
    }
}
