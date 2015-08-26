<?php

namespace Undine\Security\Rsa;

class KeychainGenerator
{
    private $bits;

    /**
     * @param int $bits
     */
    public function __construct($bits = 2048)
    {
        $this->bits = $bits;
    }

    /**
     * @return string[] An array with two elements; First is private key (with the "BEGIND/END PRIVATE KEY" header/footer and newlines),
     *                  second is public key (in base64 form; without the header/footer and newlines).
     */
    public function generateKeyPair()
    {
        $keyResource = openssl_pkey_new([
            'private_key_bits' => $this->bits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($keyResource, $privateKey);
        $publicKey = (openssl_pkey_get_details($keyResource)['key']);

        return [$privateKey, $publicKey];
    }

    /**
     * @param string $publicKey
     *
     * @return string
     */
    private function stripHeaderAndFooter($publicKey)
    {
        // Remove header and footer; -----BEGIN CERTIFICATE----- and -----END CERTIFICATE-----.
        $publicKey = preg_replace('{^-.*$}m', '', $publicKey);
        // Remove new lines.
        $publicKey = str_replace(["\r", "\n", ' '], '', $publicKey);
        if (!preg_match('{^[a-zA-Z\d/+]+={0,2}$}', $publicKey)) {
            throw new \RuntimeException('Did not result in a valid key; this should never happen.');
        }

        return $publicKey;
    }
}
