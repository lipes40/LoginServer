<?php

function encrypt_aes_gcm(string $plaintext, string $key): string {
    $method = 'aes-256-gcm';
    $iv = random_bytes(openssl_cipher_iv_length($method));
    $tag = '';
    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function decrypt_aes_gcm(string $b64, string $key): ?string {
    $data = base64_decode($b64, true);
    if ($data === false) return null;
    $method = 'aes-256-gcm';
    $ivlen = openssl_cipher_iv_length($method);
    $taglen = 16;
    $iv = substr($data, 0, $ivlen);
    $tag = substr($data, $ivlen, $taglen);
    $ciphertext = substr($data, $ivlen + $taglen);
    $plaintext = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $plaintext === false ? null : $plaintext;
}