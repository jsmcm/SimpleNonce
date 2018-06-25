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
Click here to <a href="deleteUser.php?nonce=<?php print $nonceValues["nonce"]; ?>&timeStamp=<?php print $nonceValues["timeStamp"]; ?>&userID=<?php print $userID; ?>">Delete user</a>
</body>
</html>
