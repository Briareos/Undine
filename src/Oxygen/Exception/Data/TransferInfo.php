<?php

namespace Undine\Oxygen\Exception\Data;

/**
 * @property string|null $url
 * @property string|null $content_type
 * @property int|null    $http_code
 * @property int|null    $header_size
 * @property int|null    $request_size
 * @property int|null    $filetime
 * @property int|null    $ssl_verify_result
 * @property int|null    $redirect_count
 * @property float|null  $total_time
 * @property float|null  $namelookup_time
 * @property float|null  $connect_time
 * @property float|null  $pretransfer_time
 * @property int|null    $size_upload
 * @property int|null    $size_download
 * @property int|null    $speed_download
 * @property int|null    $speed_upload
 * @property int|null    $download_content_length
 * @property int|null    $upload_content_length
 * @property float|null  $starttransfer_time
 * @property int|null    $redirect_time
 * @property string|null $redirect_url
 * @property string|null $primary_ip
 * @property array       $certinfo
 * @property int|null    $primary_port
 * @property string|null $local_ip
 * @property int|null    $local_port
 * @property int|null    $errno
 * @property string|null $error
 */
class TransferInfo
{
    private static $defaultTransferInfo = [
        'url' => null,
        'content_type' => null,
        'http_code' => null,
        'header_size' => null,
        'request_size' => null,
        'filetime' => null,
        'ssl_verify_result' => null,
        'redirect_count' => null,
        'total_time' => null,
        'namelookup_time' => null,
        'connect_time' => null,
        'pretransfer_time' => null,
        'size_upload' => null,
        'size_download' => null,
        'speed_download' => null,
        'speed_upload' => null,
        'download_content_length' => null,
        'upload_content_length' => null,
        'starttransfer_time' => null,
        'redirect_time' => null,
        'redirect_url' => null,
        'primary_ip' => null,
        'certinfo' => [],
        'primary_port' => null,
        'local_ip' => null,
        'local_port' => null,
        // Error info.
        // Result of calling curl_errno() on the handle.
        'errno' => null,
        // Result of calling curl_error() on the handle.
        'error' => null,
    ];

    /**
     * @var array
     */
    private $transferInfo = [];

    public function __construct(array $transferInfo)
    {
        $this->transferInfo = $transferInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        if (isset($this->transferInfo[$name])) {
            return $this->transferInfo[$name];
        }

        if (isset(self::$defaultTransferInfo[$name])) {
            return self::$defaultTransferInfo[$name];
        }

        throw new \OutOfRangeException(sprintf('The property "%s" does not exist.', $name));
    }
}
