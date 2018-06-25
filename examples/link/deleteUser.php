<?php

include(__DIR__."/../../src/SimpleNonce.php");

$oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();

$nonce = filter_var($_GET["nonce"], FILTER_SANITIZE_STRING);
$timeStamp = filter_var($_GET["timeStamp"], FILTER_SANITIZE_STRING);
$userID = intVal($_GET["userID"]);

$action = "deleteUser";
$meta = [$userID];

if (! $oSimpleNonce->verifyNonce($nonce, $action, $timeStamp, $meta)) {
    print "Nonce failed!";
    exit();
}
?>
<html>
<body>
Nonce passed! Deleting...
<p>
Try refreshing this page or using the link again in another browser window...  

</body>
</html>
