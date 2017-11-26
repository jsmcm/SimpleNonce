<?php

include(__DIR__."/../../class.SimpleNonce.php");

$oSimpleNonce = new SimpleNonce();

$UserID = 1; // This is the user account we're about to delete

$Action = "deleteUser";
$Meta = [$UserID];

$NonceValues = $oSimpleNonce->GenerateNonce($Action, $Meta);
?>
<html>
<body>
Click here to <a href="deleteUser.php?Nonce=<?php print $NonceValues["Nonce"]; ?>&TimeStamp=<?php print $NonceValues["TimeStamp"]; ?>&UserID=<?php print $UserID; ?>">Delete user</a>
</body>
</html>
