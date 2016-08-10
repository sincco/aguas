<?php

class Google2StepHelper {
    static $PASS_CODE_LENGTH = 6;
    static $PIN_MODULO;
    static $SECRET_LENGTH = 10;
    
    public function __construct() {
        self::$PIN_MODULO = pow(10, self::$PASS_CODE_LENGTH);
    }
    
    public function validaCodigo($secret,$code) {
        $time = floor(time() / 30);
        for ( $i = -1; $i <= 1; $i++) {
            
            if ($this->generaCodigo($secret,$time + $i) == $code) {
                return true;
            }
        }
        
        return false;
        
    }
    
    public function generaCodigo($secret,$time = null) {
        include_once(PATH_ROOT.'/app/Helpers/FixedBitNotation.php');
        if (!$time) {
            $time = floor(time() / 30);
        }
        $base32 = new FixedBitNotation(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', TRUE, TRUE);
        $secret = $base32->decode($secret);
        
        $time = pack("N", $time);
        $time = str_pad($time,8, chr(0), STR_PAD_LEFT);
        
        $hash = hash_hmac('sha1',$time,$secret,true);
        $offset = ord(substr($hash,-1));
        $offset = $offset & 0xF;
        
        $truncatedHash = self::hashToInt($hash, $offset) & 0x7FFFFFFF;
        $pinValue = str_pad($truncatedHash % self::$PIN_MODULO,6,"0",STR_PAD_LEFT);;
        return $pinValue;
    }
    
    protected  function hashToInt($bytes, $start) {
        $input = substr($bytes, $start, strlen($bytes) - $start);
        $val2 = unpack("N",substr($input,0,4));
        return $val2[1];
    }
    
    public function urlQR($user, $hostname, $secret) {
        $url =  urlencode(sprintf("otpauth://totp/%s@%s?secret=%s", $user, $hostname, $secret));
        //var_dump($url);
        //https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=
        $encoderURL = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=".$url;
        //$encoderURL = sprintf( "%sotpauth://totp/%s@%s&secret=%s",$encoder, $user, $hostname, $secret);
        
        return $encoderURL;
        
    }
    
    public function generaLlave() {
        $secret = "";
        for($i = 1;  $i<= self::$SECRET_LENGTH;$i++) {
            $c = rand(0,255);
            $secret .= pack("c",$c);
        }
        $base32 = new FixedBitNotation(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', TRUE, TRUE);
        return  $base32->encode($secret);
        
        
    }
    
}

