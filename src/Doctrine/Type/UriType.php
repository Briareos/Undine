<?php

namespace Undine\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class UriType extends Type
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
        return 'uri';
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /* @var UriInterface|null $value */
        return ($value === null) ? null : (string)$value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof UriInterface) {
            return $value;
        }

        try {
            $val = new Uri($value);
        } catch (\InvalidArgumentException $e) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), 'URI (RFC 3986)');
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
