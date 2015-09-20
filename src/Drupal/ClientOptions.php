<?php

namespace Undine\Drupal;

final class ClientOptions
{
    /**
     * Fill in with an instance of FtpCredentials to automatically submit Drupal's "FTP connection settings" form.
     *
     * If the form is encountered and credentials are not present, an FtpCredentialsRequiredException will be thrown.
     * If the form is encountered and credentials are present, the form will be submitted. If the form is still
     * present in the next request, an FtpCredentialsErrorException will be thrown.
     *
     * @see FtpCredentials
     * @see FtpCredentialsRequiredException
     * @see FtpCredentialsErrorException
     */
    const FTP_CREDENTIALS = 'ftp_credentials';
}
