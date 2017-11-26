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

<form action="deleteUser.php" method="POST">
<input type="hidden" name="UserID" value="<?php print $UserID; ?>">
<input type="hidden" name="Nonce" value="<?php print $NonceValues["Nonce"]; ?>">
<input type="hidden" name="TimeStamp" value="<?php print $NonceValues["TimeStamp"]; ?>">

<input type="submit" value="Delete user">
</form>

</body>
</html>
