<?php

namespace Undine\Functions;

use Undine\Functions\Exception\OpensslSignException;

/**
 * @param int $bits
 *
 * @return string[] An array with two elements; first is private key (with the "BEGIND/END PRIVATE KEY" header/footer and newlines),
 *                  second is public key (with the "BEGIN/END PUBLIC KEY" header/footer and newlines).
 */
function openssl_generate_rsa_key_pair($bits = 2048)
{
    $keyResource = openssl_pkey_new([
        'private_key_bits' => $bits,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    openssl_pkey_export($keyResource, $privateKey);
    $publicKey = (openssl_pkey_get_details($keyResource)['key']);

    return [$privateKey, $publicKey];
}

/**
 * @param string $privateKey Private key in base64-encoded form.
 * @param string $data Any data up to
 *
 * @return string Signature in base64-encoded form, to be consistent with public/private key formats.
 *
 * @throws OpensslSignException
 */
function openssl_sign_data($privateKey, $data)
{
    $signed = @openssl_sign($data, $signature, $privateKey);

    if (!$signed) {
        $lastError    = error_get_last();
        $opensslError = '';

        while (($opensslErrorRow = openssl_error_string()) !== false) {
            $opensslError = $opensslErrorRow."\n".$opensslError;
        }

        throw new OpensslSignException(sprintf('Failed to sign data using private key; last error: %s; OpenSSL error: %s', $lastError['message'], $opensslError));
    }

    return base64_encode($signature);
}
