<?php

namespace Undine\Uid;

use Jenssegers\Optimus\Optimus;
use Doctrine\Common\Util\ClassUtils;

/**
 * This method must be accessible for the trait to work.
 * @method getId
 */
trait UidTrait
{
    private static $maxId = Optimus::MAX_INT;

    /**
     * @return array An array of two elements; first is string (prefix), second is an Optimus instance.
     *
     * @see Optimus
     */
    private static function getPrefixAndEncoder()
    {
        static $prefixAndEncoder;

        if ($prefixAndEncoder === null) {
            $className = ClassUtils::getRealClass(get_called_class());
            if (!isset($GLOBALS['uid_registry'][$className]) || count($GLOBALS['uid_registry'][$className]) !== 4) {
                throw new \RuntimeException(sprintf("The \$GLOBALS['uid_registry']['%s'] variable must be populated with 4 elements (prefix:string, prime:int, inverse:int and seed:int) for the %s trait to work.", $className, __TRAIT__));
            }
            // This part is horribly registered in AppBundle::boot().
            $uidInfo = $GLOBALS['uid_registry'][$className];
            $prefixAndEncoder = [$uidInfo[0], new Optimus($uidInfo[1], $uidInfo[2], $uidInfo[3])];
        }

        return $prefixAndEncoder;
    }

    /**
     * Returns UID in format P00000001, where "P" is the model's prefix;
     * and the rest is a numeric ID left-padded with zeros to 10 characters.
     * Prefix does not have a limit.
     *
     * @return string|null
     */
    public function getUid()
    {
        if (!is_callable([$this, 'getId'])) {
            throw new \RuntimeException(sprintf('All classes implementing %s must have an accessible "getId()" method.', __TRAIT__));
        }

        return self::getUidFromId($this->getId());
    }

    /**
     * Strip's the class's prefix from the ID and converts the remaining
     * 10-character left-padded (with zeros) numeric ID to integer and returns it.
     *
     * @param string $uid
     *
     * @return int|null ID if the UID is valid; null otherwise.
     */
    public static function getIdFromUid($uid)
    {
        /** @var string $prefix */
        /** @var Optimus $encoder */
        list($prefix, $encoder) = self::getPrefixAndEncoder();

        if (substr($uid, 0, strlen($prefix)) !== $prefix) {
            // The prefix doesn't match.
            return null;
        }

        if (!$id = (int)$encoder->decode(ltrim(substr($uid, -10), '0'))) {
            return null;
        }

        return $id;
    }

    /**
     * @param int $id
     *
     * @return string|null UID if the ID is valid; null otherwise.
     */
    public static function getUidFromId($id)
    {
        if ((string)((int)$id) !== (string)$id) {
            // ID is not numeric.
            return null;
        }
        $id = (int)$id;

        if ($id < 1 || $id > self::$maxId) {
            return null;
        }

        /** @var string $prefix */
        /** @var Optimus $encoder */
        list($prefix, $encoder) = self::getPrefixAndEncoder();

        return sprintf($prefix."%'.010s", strtoupper($encoder->encode($id)));
    }
}
