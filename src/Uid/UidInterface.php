<?php

namespace Undine\Uid;

interface UidInterface
{
    /**
     * @return string|null UID, or NULL if the ID is not set.
     */
    public function getUid();

    /**
     * @param string $uid
     *
     * @return int|null ID or NULL if the UID is invalid.
     */
    public static function getIdFromUid($uid);

    /**
     * @param int $id
     *
     * @return string|null UID or NULL if ID is invalid.
     */
    public static function getUidFromId($id);
}
