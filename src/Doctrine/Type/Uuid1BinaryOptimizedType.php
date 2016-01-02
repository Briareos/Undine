<?php

namespace Undine\Doctrine\Type;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @link https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
 */
class Uuid1BinaryOptimizedType extends Type
{
    const NAME = 'uuid1_binary_optimized';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBinaryTypeDeclarationSQL(
            [
                'length' => 16,
                'fixed'  => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if (strlen($value) !== 16) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }

        $unpacked = unpack('H4p3/H4p2/H8p1/H4p4/H12p5', $value);

        return $unpacked['p1'].'-'.$unpacked['p2'].'-'.$unpacked['p3'].'-'.$unpacked['p4'].'-'.$unpacked['p5'];
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if (!\Undine\Functions\valid_uuid1($value)) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }

        list($p1, $p2, $p3, $p4, $p5) = explode('-', $value);

        return hex2bin($p3.$p2.$p1.$p4.$p5);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
