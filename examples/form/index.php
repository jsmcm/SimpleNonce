<?php

include(__DIR__."/../../src/SimpleNonce.php");

$oSimpleNonce = new \SoftSmart\Utilities\SimpleNonce();

$userID = 1; // This is the user account we're about to delete

$action = "deleteUser";
$meta = [$userID];

$nonceValues = $oSimpleNonce->generateNonce($action, $meta);
?>
<html>
<body>

<form action="deleteUser.php" method="POST">
<input type="hidden" name="userID" value="<?php print $userID; ?>">
<input type="hidden" name="nonce" value="<?php print $nonceValues["nonce"]; ?>">
<input type="hidden" name="timeStamp" value="<?php print $nonceValues["timeStamp"]; ?>">

<input type="submit" value="Delete user">
</form>

</body>
</html>
