<?php
/**
 * Created by PhpStorm.
 * User: a
 * Date: 2018-01-15
 * Time: 오전 11:07
 *
 * AES-128 encrypt / decrypt
 * # Java source 와 매칭버전
 * 2014.1.22 bgh
 */

function myEncrypt( $val ){
    $cipher = "rijndael-128";
    $mode = "cbc";
    $secret_key = "E4:1B:AF:5F:F0:BE";
    //iv length should be 16 bytes
    $iv = "fedcba9876543210";

    // Make sure the key length should be 16 bytes
    $key_len = strlen($secret_key);
    if($key_len < 16 ){
        $addS = 16 - $key_len;
        for($i =0 ;$i < $addS; $i++){
            $secret_key.=" ";
        }
    }else{
        $secret_key = substr($secret_key, 0, 16);
    }

    $td = mcrypt_module_open($cipher, "", $mode, $iv);
    mcrypt_generic_init($td, $secret_key, $iv);
    $cyper_text = mcrypt_generic( $td, $val );
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return bin2hex($cyper_text);
}

function myDecrypt( $val ){
    $cipher = "rijndael-128";
    $mode = "cbc";
    $secret_key = "E4:1B:AF:5F:F0:BE";
    //iv length should be 16 bytes
    $iv = "fedcba9876543210";

    // Make sure the key length should be 16 bytes
    $key_len = strlen($secret_key);
    if($key_len < 16 ){
        $addS = 16 - $key_len;
        for($i =0 ;$i < $addS; $i++){
            $secret_key.=" ";
        }
    }else{
        $secret_key = substr($secret_key, 0, 16);
    }
    $td = mcrypt_module_open($cipher, "", $mode, $iv);
    mcrypt_generic_init($td, $secret_key, $iv);
    $decrypted_text = mdecrypt_generic($td, hex2bin( $val ));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return trim($decrypted_text);
}

function hex2bin($data){
    $bin = "";
    $i = 0;
    do {
        $bin .= chr(hexdec($data{$i}.$data{($i + 1)}));
        $i += 2;
    } while ($i < strlen($data));

    return $bin;
}



/*
 * http://s00s10.blog.163.com/blog/static/43988552201411913011459/
 **/
function iosAES128Encode( $value ) {
    $iv = 'fedcba9876543210';
    $key = "E4:1B:AF:5F:F0:B";

    $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
    mcrypt_generic_init($td, $key, $iv);
    $encrypted = mcrypt_generic($td, $value);

    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return bin2hex($encrypted);
}


/*
 * http://s00s10.blog.163.com/blog/static/43988552201411913011459/
 **/
function iosAES128Decode( $value ) {
    $iv = 'fedcba9876543210';
    $key = "E4:1B:AF:5F:F0:B";

    //$key = $this->hex2bin($key);
    $code = hex2bin($value);

    $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

    mcrypt_generic_init($td, $key, $iv);
    $decrypted = mdecrypt_generic($td, $code);

    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return utf8_encode(trim($decrypted));
}





/*********************************************************/

?>