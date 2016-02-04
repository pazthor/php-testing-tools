<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Doctrine2\Types;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use EWallet\Accounts\Identifier;
use InvalidArgumentException;

/**
 * UUID fields will be stored as a string in the database and converted back to
 * the Identifier value object when querying.
 */
abstract class UuidType extends GuidType
{
    /**
     * @param  string|null $value
     * @param  AbstractPlatform $platform
     * @throws ConversionException
     * @return Identifier|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->identifier($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    /**
     * @param  Identifier|null $value
     * @param  AbstractPlatform $platform
     * @throws ConversionException
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Identifier) {
            return (string) $value;
        } elseif (is_string($value)) {
            return $value;
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * @param  string|null $value
     * @return Identifier
     */
    abstract public function identifier($value);
}
