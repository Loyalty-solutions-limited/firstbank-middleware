<?php

namespace App\Services;
use App\Models\Enrollment;
class MigrationService {
    public static $key = '!QAZXSW@#EDCVFR$';
    public static $iv = '5666685225155700';
    protected static $program = "Firstbank Green Rewards Programme";
    protected static $placeholders = array('$memberID', '$first_name', '$last_name', '$pin', '$points', '$email', '$program');
    protected static $link = "https://firstreward.firstbanknigeria.com/login.php";


    protected static $headerPayload = array(
        //'Content-Type: application/json',
    );
    protected static $url = "https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/api/v1/index.php";
    // protected static $url = "https://fbn.perxclm5.com/api/v1/index.php";

    public function __construct()
    {

    }
    protected static function pushToPERX($url="https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/api/v1/index.php", $postFields, $payload)
    {

        $curl= curl_init();
        $ch = $curl;
        $timeout = 0; // Set 0 for no timeout.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type= application/json"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $result = curl_exec($ch);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch). "^^";
}else{
	$error_msg = '';
}

        curl_close($ch);

        return $result;
    }
    public static function string_encrypt($string, $key, $iv) : string{
        $ciphering = "AES-128-CTR";
        $encryption_iv = '1234567891011121';
        $key = "SmoothJay";
        $options = 0;
        $encryption = openssl_encrypt($string, $ciphering,
            $key, $options, $iv);
        return $encryption;
    }

    public static function string_decrypt($encryption, $key, $iv) : string{
         $ciphering = "AES-128-CTR";
        $decryption_iv = '1234567891011121'; //1234567891011121
        $key = "SmoothJay";
        $options = 0;
        $decryption=openssl_decrypt($encryption, $ciphering,
        $key, $options, $iv);
        return $decryption;
    }

public static function passwordReturn(){
    return self::string_encrypt('Di@mond10$#', self::$key,self::$iv);
}
   // self::$password = parent::string_encrypt('Di@mond10$#', self::$key,self::$iv);



public static function resolveMemberReference($cif_id){
    $cif_id = Enrollment::where('cif_id', $cif_id)->select('cif_id')->first();
    return $cif_id ? $cif_id->cif_id:null;
}

public static function isJSON($string){
	return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

}
?>