<?php
if (!function_exists('generate_pass')) {
	function generate_pass($pass) {
        $key_encrypt = "3nt30p3r@";
        
        // CBC has an IV and thus needs randomness every time a message is encrypted
        $method = 'aes-256-cbc';
        
        // Must be exact 32 chars (256 bit)
        // You must store this secret random key in a safe place of your system.
        $key = substr(hash('sha256', $key_encrypt, true), 0, 32);
        //echo "Password:" . $password . "\n";
        
        // Most secure key
        //$key = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        
        // IV must be exact 16 chars (128 bit)
        $iv = chr(0x15) . chr(0x14) . chr(0x2) . chr(0x3) . chr(0x11) . chr(0x10) . chr(0x6) . chr(0x7) . chr(0x8) . chr(0x9) . chr(0x5) . chr(0x4) . chr(0x12) . chr(0x13) . chr(0x1) . chr(0x0);
    
        // Most secure iv
        // Never ever use iv=0 in real life. Better use this iv:
        // $ivlen = openssl_cipher_iv_length($method);
        // $iv = openssl_random_pseudo_bytes($ivlen);
        
        // av3DYGLkwBsErphcyYp+imUW4QKs19hUnFyyYcXwURU=
        $encrypted = base64_encode(openssl_encrypt($pass, $method, $key, OPENSSL_RAW_DATA, $iv));

        return $encrypted;
    }
}
