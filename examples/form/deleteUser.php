<?php

include(__DIR__."/../../class.SimpleNonce.php");

$oSimpleNonce = new SimpleNonce();

$Nonce = filter_var($_POST["Nonce"], FILTER_SANITIZE_STRING);
$TimeStamp = filter_var($_POST["TimeStamp"], FILTER_SANITIZE_STRING);
$UserID = intVal($_POST["UserID"]);

$Action = "deleteUser";
$Meta = [$UserID];

if( ! $oSimpleNonce->VerifyNonce($Nonce, $Action, $TimeStamp, $Meta) )
{
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