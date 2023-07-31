<?php
namespace Gemstone\gemsecure;

use GemstoneHardnessCalculator;
use GemstoneHasher;
use Throwable;

require __DIR__."/log/config.php";
require __DIR__."/GemstoneHasher.php";
require __DIR__."/GemstoneHardnessCalculator.php";

class GemstoneLoader {

    private $config;
    private $configvalue;
    private $hashvalue;
    private $dataenc;
    private $deckey;

    
    // For constructor 
    public function __construct($config, $configvalue , $hashvalue)
    {
        $this->config = $config;
        $this->configvalue = $configvalue;
        $this->hashvalue =  $hashvalue;
        
    }

    public function EncGem($data, $key) {
        $serialized = json_encode($data);
        return $this->arrayenc($serialized, $key);
    }

    public function arrayenc($serialized, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
        $ciphertext = openssl_encrypt($serialized, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $hexconverter = bin2hex($iv).bin2hex($ciphertext);
        //$slice = substr($hexconverter,'0','32');
        return $hexconverter;
//        return $iv . $ciphertext;
    }

    public function DecGem($data, $key) {
        $dec = hex2bin($data);
        $decrypted = $this->arraydec($dec, $key);
        return json_decode($decrypted, true);
    }

    public function arraydec($ciphertext, $key) {
        $iv_length = openssl_cipher_iv_length('aes-128-cbc');
        $iv = substr($ciphertext, 0, $iv_length);
        $ciphertext = substr($ciphertext, $iv_length);
        $data = openssl_decrypt($ciphertext, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return $data;
    }

    // generate new Gemstone Hashing Method

    public function ReGem() {

        try{
            $gemcalc = new GemstoneHardnessCalculator();
            $gemcalc->generateHashValues(); 

            echo "Step 1 : Running Gemstone Task"."<br>";

            try{
                $gemhash = new GemstoneHasher();
                $gemhash->storeHashedValues();
                echo "Step 2 : New Gemstone Encryptions";

            }catch(Throwable $e) {
                echo "error:".$e->getMessage();
            }

        }catch(Throwable $e){
            echo "error:".$e->getMessage();
        }
        
    }

}
