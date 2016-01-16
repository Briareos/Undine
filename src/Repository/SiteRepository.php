<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Undine\Model\Site;

class SiteRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    public function __construct(EntityManager $em, ClassMetadata $metadata)
    {
        $this->em = $em;
        $this->metadata = $metadata;
    }

    /**
     * @param string $id Site UUID.
     *
     * @return Site|null
     */
    public function find($id)
    {
        if (!\Undine\Functions\valid_uuid($id)) {
            return null;
        }

        return $this->em->find(Site::class, (string)$id);
    }

    /**
     * @param \DateTime $maximumThumbnailAge
     *
     * @return SiteData\SiteForThumbnailUpdate|null
     */
    public function yieldSiteForThumbnailUpdate(\DateTime $maximumThumbnailAge)
    {
        $selectQuery = <<<SQL
SELECT s.id, s.url, s.http_username, s.http_password, s.thumbnailPath
FROM Site s
WHERE
  (
    # Thumbnail is not set...
    s.thumbnailPath IS NULL
    # ... or thumbnail is at least this old.
    OR (s.thumbnailUpdatedAt < :lockTimeout)
  )
  AND
  (
    # There is no lock...
    s.thumbnailLockedAt IS NULL
    OR
    # .. or the lock has expired.
    s.thumbnailLockedAt < :lockTimeout
  )
ORDER BY
  # Prioritize sites without a thumbnail.
  s.thumbnailPath IS NULL DESC
SQL;

        $data = $this->em->getConnection()->fetchAssoc($selectQuery, [
            'lockTimeout' => $maximumThumbnailAge->format('Y-m-d H:i:s'),
        ]);

        if ($data === false) {
            return null;
        }

        return new SiteData\SiteForThumbnailUpdate(\Undine\Functions\binary_to_uuid($data['id']), $data['url'], $data['http_username'], $data['http_password'], $data['thumbnailPath']);
    }

    /**
     * @param string    $id Site's ID.
     * @param \DateTime $maximumThumbnailAge
     *
     * @return bool Whether the lock was successful.
     */
    public function lockSiteForThumbnailUpdate($id, \DateTime $maximumThumbnailAge)
    {
        $lockQuery = <<<SQL
UPDATE Site
    SET thumbnailLockedAt = :now
WHERE id = :id
  AND (thumbnailLockedAt IS NULL OR site.thumbnailLockedAt < :lockTimeout)
SQL;

        return (bool)$this->em->getConnection()->executeUpdate($lockQuery, [
            'id' => \Undine\Functions\uuid_to_binary($id),
            'now' => (new \DateTime())->format('Y-m-d H:i:s'),
            'lockTimeout' => $maximumThumbnailAge->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param string $id
     * @param string $thumbnailPath
     */
    public function updateSiteThumbnail($id, $thumbnailPath)
    {
        $updateQuery = <<<SQL
UPDATE Site
SET
  thumbnailPath = :thumbnailPath,
  thumbnailUpdatedAt = :thumbnailUpdatedAt,
  thumbnailLockedAt = NULL
WHERE id = :id
SQL;

        $this->em->getConnection()->executeUpdate($updateQuery, [
            'id' => \Undine\Functions\uuid_to_binary($id),
            'thumbnailPath' => $thumbnailPath,
            'thumbnailUpdatedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }
}
