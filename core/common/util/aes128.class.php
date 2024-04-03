<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 8:45
 */
class aes128
{
    private $cipher = "rijndael-128";
    private $mode = "cbc";
    private $secret_key = "salkf!gsek@ugjwe"; // Make sure the key length should be 16 bytes
    private $iv = "feacba9876543211"; //iv length should be 16 bytes

    /**
     * 암호화
     * @param $val
     * @return string
     */
    public function encrypt($val)
    {
        $td = mcrypt_module_open($this->cipher, "", $this->mode, $this->iv);
        mcrypt_generic_init($td, $this->secret_key, $this->iv);
        $cyper_text = mcrypt_generic($td, $val);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return bin2hex($cyper_text);
    }

    /**
     * 복호화
     * @param $val
     * @return string
     */
    public function decrypt($val)
    {
        $td = mcrypt_module_open($this->cipher, "", $this->mode, $this->iv);
        mcrypt_generic_init($td, $this->secret_key, $this->iv);
        $decrypted_text = mdecrypt_generic($td, $this->hex2bin($val));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim($decrypted_text);
    }

    /**
     * hex to bin
     * @param $data
     * @return string
     */
    private function hex2bin($data)
    {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($data{$i} . $data{($i + 1)}));
            $i += 2;
        } while ($i < strlen($data));

        return $bin;
    }
}