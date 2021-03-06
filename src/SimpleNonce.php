<?php
/**
* A class to generate and verify nonces.
*
* PHP version 7.2
*
* GNU General Public License v3
*
* @category  Utilities
* @package   SimpleNonce
* @author    John McMurray <john@softsmart.co.za>
* @copyright 2017-2027 SoftSmart.co.za
* @license   gnu v3
* @link      http://softsmart.co.za/projects/simple-nonce/
*/

namespace SoftSmart\Utilities;

/**
* Class.SimpleNonce.php
*
* @category  Utilities
* @package   SimpleNonce
* @author    John McMurray <john@softsmart.co.za>
* @copyright 2017-2027 SoftSmart.co.za
* @license   gnu v3
* @link      http://softsmart.co.za/projects/simple-nonce/
*/
class SimpleNonce
{
    // change these settings in config.inc.php
    protected $nonceSalt = "DefaultNoncePleaseFix";
    protected $nonceExpiryTime = 3600; // In seconds. 3600 = 1 hour
    protected $path;
    
    /**
    * Constructor. Set's the tmp directory and calls the protected
    * function manageNonceTempFiles which deletes stale tmp files
    */
    function __construct($runtime_config = []) 
    {
        include dirname(__FILE__)."/config.inc.php";
    
    	if (isset($runtime_config["salt"])) {
    		$this->nonceSalt = $runtime_config["salt"];
        } else if (isset($config["salt"])) {
            $this->nonceSalt = $config["salt"];
        }
    
    	if (isset($runtime_config["ttl"])) {
    		$this->nonceExpiryTime = $runtime_config["ttl"];
        } else if (isset($config["ttl"])) {
            $this->nonceExpiryTime = $config["ttl"];
        }
    
        // where will the nonce files be saved
        $this->path = dirname(__FILE__)."/nonce"; 
        if (isset($runtime_config["tmpPath"])) {
        	$this->path = $runtime_config["tmpPath"];
        } else if (isset($config["tmpPath"])) {
            $this->path = $config["tmpPath"];
        }
    
        // test the path
        if (! file_exists($this->path)) {
    
            try {
                mkdir($this->path);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }    
            
        }

        $this->manageNonceTempFiles();
    }


    /**
    * Clean up tmp nonce verification files
    *
    * @return null
    */
    protected function manageNonceTempFiles()
    {
    
        // Start off by deleting stale nonce files
        $dh = opendir($this->path);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {

                $fullPath = $this->path."/".$file;

                if (file_exists($fullPath)) {
                    if ((time() - filemtime($fullPath)) > ($this->nonceExpiryTime)) {
                        unlink($fullPath);
                    }
                }
            }
        }

        closedir($dh);
    }


    /**
    * Verify a given nonce
    *
    * @param string $inputNonce the nonce token to verify
    * @param string $action     the action that this is verifying, eg, add_user
    * @param string $timeStamp  the expiry timestamp generated by generateNonce
    * @param mixed  $metaData   optional salts for the nonce, eg, user_id
    * @param int    $expiry     override the default expiry time
    * @param bool   $multi      can this nonce be used multiple times (much like WP nonces)
    * 
    * @return boolean
    */
    public function verifyNonce($inputNonce, $action, $timeStamp, $metaData=null, $expiry=0, $multi=false)
    {

        if ( $expiry == 0 ) {
            $expiry = $this->nonceExpiryTime;
        }
        $expires = $timeStamp + $expiry;
        $now = time();

        if ($expires - $now < 0) {
            return false;
        }

        $nonceSeedString = $action;

        if (is_array($metaData) && $metaData != null) {
            foreach ($metaData as $meta) {
                $nonceSeedString = $nonceSeedString.$meta;
            }
        }

        $nonceSeedString = $nonceSeedString.$timeStamp;
        $nonceSeedString = $nonceSeedString.$this->nonceSalt;

        $nonce = md5($nonceSeedString);

        if ($nonce != $inputNonce) {
            return false;
        }


        if ( $multi == true ) {
            // Its valid and not expired + multi is true
            // so don't check if its already been used
            return true;
        }


        $actionBuffer = $action;
        for ($x = 0; $x < strlen($action); $x++) {
            if (! ctype_alnum(substr($action, $x, 1))) {
                $actionBuffer = substr($action, 0, $x);
                $actionBuffer = $actionBuffer."_";
                $actionBuffer = $actionBuffer.substr($action, $x + 1);
                $action = $actionBuffer;
            }
        }

        if (file_exists($this->path."/".$action.$nonce)) {
            return false;
        }

        touch($this->path."/".$action.$nonce);

        return true;

    }

    /**
    * Generate a nonce with an expiry date / time
    *
    * @param string $action   the action that this is verifying, eg, add_user
    * @param mixed  $metaData optional salts for the nonce, eg, user_id
    * 
    * @return boolean
    */
    public function generateNonce($action, $metaData=null )
    {
        $returnArray = array();

        $nonceSeedString = $action;
        $timeStamp = time();

        if (is_array($metaData) && $metaData != null) {
            foreach ($metaData as $meta) {
                $nonceSeedString = $nonceSeedString.$meta;
            }
        }

        $nonceSeedString = $nonceSeedString.$timeStamp;
        $nonceSeedString = $nonceSeedString.$this->nonceSalt;

        $nonce = md5($nonceSeedString);

        $returnArray["nonce"] = $nonce;
        $returnArray["timeStamp"] = $timeStamp;

        return $returnArray;
    }
}