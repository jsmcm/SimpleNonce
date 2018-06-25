<?php
require dirname(__DIR__)."/src/SimpleNonce.php";

class SimpleNonceTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerateANonce()
    {
        $oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();
	
	$action = "test";
	$meta = ["testGenerateANonce"];
	
	$result = $oSimpleNonce->generateNonce($action, $meta);
	
	$this->assertNotEmpty($result);
	
    }
    
    
    public function testVerifyANonce()
    {
        $oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();
	
	$action = "test";
	$meta = ["testVerifyANonce"];
	
	$nonce = $oSimpleNonce->generateNonce($action, $meta);
	
	$bool = $oSimpleNonce->verifyNonce($nonce["nonce"], $action, $nonce["timeStamp"], $meta);
	
	$this->assertTrue($bool);
    }
    
    
    public function testNonceVerifiesOnlyOnce()
    {
        $oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();
	
	$action = "test";
	$meta = ["testNonceVerifiesOnlyOnce"];
	
	$nonce = $oSimpleNonce->generateNonce($action, $meta);
	
	$bool = $oSimpleNonce->verifyNonce($nonce["nonce"], $action, $nonce["timeStamp"], $meta);
	$bool = $oSimpleNonce->verifyNonce($nonce["nonce"], $action, $nonce["timeStamp"], $meta);
	
	$this->assertFalse($bool);    
    }
    
    
    
    
    public function testNonceFailsWhenTimeStampChanged()
    {
   
        $oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();
	
	$action = "test";
	$meta = ["testNonceFailsWhenTimeStampChanged"];
	
	$nonce = $oSimpleNonce->generateNonce($action, $meta);
	
	$timeStamp = time()-1;
	
	$bool = $oSimpleNonce->verifyNonce($nonce["nonce"], $action, $timeStamp, $meta);
	
	$this->assertFalse($bool); 
    }


}
