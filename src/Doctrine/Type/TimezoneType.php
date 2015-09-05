<?php

namespace Undine\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class TimezoneType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'timezone';
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /** @var \DateTimeZone|null $value */
        return ($value === null) ? null : $value->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeZone) {
            return $value;
        }

        try {
            $val = new \DateTimeZone($value);
        } catch (\Exception $e) {
            // Exception message example: DateTimeZone::__construct(): Unknown or bad timezone (foobar)
            throw ConversionException::conversionFailedFormat($value, $this->getName(), 'Valid timezone');
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        // Doctrine fields map database fields 1:1 to PHP classes. The 'varchar' type that is underlying to this type
        // would resolve to Doctrine\DBAL\Types\StringType, so we use SQL field comment hint to resolve to this type.
        return true;
    }
}
