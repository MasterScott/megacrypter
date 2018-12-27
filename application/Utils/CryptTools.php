<?php

class Utils_CryptTools
{
    public static function aesCbcEncrypt($data, $key, $iv = null, $pkcs7pad = true) {
        return openssl_encrypt($data, 'AES-'.(strlen($key)*8).'-CBC', $key, $pkcs7pad?OPENSSL_RAW_DATA:(OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING), is_null($iv) ? pack('x' . openssl_cipher_iv_length('AES-256-CBC')) : $iv);
    }

    public static function aesCbcDecrypt($data, $key, $iv = null, $pkcs7pad = true) {
        return openssl_decrypt($data, 'AES-'.(strlen($key)*8).'-CBC', $key, $pkcs7pad?OPENSSL_RAW_DATA:(OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING), is_null($iv) ? pack('x' . openssl_cipher_iv_length('AES-256-CBC')) : $iv);
    }

    public static function aesCbcEncryptI32a(array $i32a, $key, $iv = null, $pkcs7pad = true) {
        return Utils_MiscTools::bin2i32a(self::aesCbcEncrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv, $pkcs7pad));
    }

    public static function aesCbcDecryptI32a(array $i32a, $key, $iv = null, $pkcs7pad = true) {
        return Utils_MiscTools::bin2i32a(self::aesCbcDecrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv, $pkcs7pad));
    }

    public static function aesEcbEncrypt($data, $key, $iv = null, $pkcs7pad = true) {
        return openssl_encrypt($data, 'AES-'.(strlen($key)*8).'-ECB', $key, $pkcs7pad?OPENSSL_RAW_DATA:(OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING), is_null($iv) ? pack('x' . openssl_cipher_iv_length('AES-256-ECB')) : $iv);
    }

    public static function aesEcbDecrypt($data, $key, $iv = null, $pkcs7pad = true) {
        return openssl_decrypt($data, 'AES-'.(strlen($key)*8).'-ECB', $key, $pkcs7pad?OPENSSL_RAW_DATA:(OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING), is_null($iv) ? pack('x' . openssl_cipher_iv_length('AES-256-ECB')) : $iv);
    }

    public static function aesEcbEncryptI32a(array $i32a, $key, $iv = null, $pkcs7pad = true) {
        return Utils_MiscTools::bin2i32a(self::aesEcbEncrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv, $pkcs7pad));
    }

    public static function aesEcbDecryptI32a(array $i32a, $key, $iv = null, $pkcs7pad = true) {
        return Utils_MiscTools::bin2i32a(self::aesEcbDecrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv, $pkcs7pad));
    }

    public static function hash_equals($str1, $str2) {

        if( ($l=strlen($str1)) != strlen($str2) ) {
        
            return false;

        } else {
        
            $xor = $str1 ^ $str2;
            
            $ret = 0;
        
            for($i = $l - 1; $i >= 0; $i--) {

                $ret |= ord($xor[$i]);
            }
            
            return !$ret;
        }
    }

    public static function decryptMegaDownloaderLinks($data) {
        
        return preg_replace_callback('/mega\:\/\/(?P<folder>f)?(?P<enc>enc\d*?)\?(?P<linkdata>[\da-z_,-]*?)(?=https?\:|mega\:|[^\da-z_,-]|$)/i', 
                
            function($match) {
				
                $key = ['enc' => '6B316F36416C2D316B7A3F217A30357958585858585858585858585858585858', 
						'enc2' => 'ED1F4C200B35139806B260563B3D3876F011B4750F3A1A4A5EFD0BBE67554B44'];

                $iv = '79F10A01844A0B27FF5B2D4E0ED3163E';

                return Utils_MegaApi::MEGA_HOST . '/#' . strtoupper($match['folder']) . Utils_CryptTools::aesCbcDecrypt(Utils_MiscTools::urlBase64Decode($match['linkdata']), hex2bin($key[$match['enc']]), hex2bin($iv)); }, 
                
            $data);
    }

}
