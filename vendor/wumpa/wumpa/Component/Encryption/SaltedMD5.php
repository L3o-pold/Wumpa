<?php

namespace Wumpa\Component\Encryption;

/**
 * This class offer a way to encrypt data using MD5 salted.
 * Encrypted data will be like: SALT:DATA
 *
 * It can be used to encrypt password.
 */
class SaltedMD5 {

    const ALPHA_NUM = 0;
    const ALPHA = 1;
    const NUM = 2;
    const CUSTOM = 3;


    public static function crypt($dataToEncrypt, $salt) {
        return $salt.":".md5($salt.$dataToEncrypt);
    }

    public static function isSame($encryptedData, $dataToTest) {
        $part = explode(":", $encryptadData);
        $salt = $part[0];

        if(md5($salt.$dataToTest) == $part[1]) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateSalt($length, $mode = 0, $customArray = null) {
        switch($mode) {
            case 0:
                $chars = array();

                for($i = 0; $i < 10; $i++) {
                    $chars[] = "".$i;
                }

                $low = "a";
                for($i = 0; $i < 26; $i++) {
                    $chars[] = $low++;
                }

                $high = "A";
                for($i = 0; $i < 26; $i++) {
                    $chars[] = $high++;
                }
                break;
            case 1:
                $chars = array();

                $low = "a";
                for($i = 0; $i < 26; $i++) {
                    $chars[] = $low++;
                }

                $high = "A";
                for($i = 0; $i < 26; $i++) {
                    $chars[] = $high++;
                }
                break;
            case 2:
                $chars = array();

                for($i = 0; $i < 10; $i++) {
                    $chars[] = "".$i;
                }
                break;
            case 3:
                $chars = $customArray;
                break;
        }

        $salt = "";

        for($i = 0; $i < $length; $i++) {
            $salt .= $chars[rand(0, count($chars)-1)];
        }

        return $salt;
    }

}
