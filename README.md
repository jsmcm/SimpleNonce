# SimpleNonce
A simple Nonce implementation in PHP

This is a very simple nonce implementation.

Version: 
    1.0.0

Settings:
    1) Define your own salt in the classes protected NonceSalt variable. This can be any text.
    2) Define the expiry time of the nonce. Default is 1 hour (3600 seconds).
    3) In the constructor define the path of the temp nonce files. Default is a folder called "nonce" above the public directory.

Usage:
    First you need to generate a nonce. You then attach that nonce to a url or form and then you verify it!

    Generate Nonce:
        The GenerateNonce takes 2 arguments.

        Arg1: Action
        Arg2: MetaData Array (optional)

        The action can be any text you choose, but usually this should be descriptive of what you're doing. For example, if you are deleting a user, a good action might be "delete user", or "deleteUser", etc, depending on your coding style.

        The MetaData array can have one or more meta fields which will be used to add more salt (or pepper) to the nonce key.

        So, for example, if I am deleting a user, I might pass the user ID as meta data, eg:

            $Action = "deleteUser";
            $Meta = [$UserID];

            $NonceValues = SimpleNonce::GenerateNonce($Action, $Meta);

        The benefit of adding in the meta data is that you have a very specific action you're protecting. If you used just the action then someone who managed to get the nonce and the link before you used it could delete a different user using this nonce. However, by adding in the user ID as meta data, the nonce will work only for that specific user.

        GenerateNonce returns an array with two fields:
            Nonce: The actual nonce string.
            TimeStamp: a timestamp of when the nonce was generated. This determines the expiry time of the nonce.

            BOTH of these fields must be added to your URL or form.

            Eg, continuing with our example above:

                <?php
                $UserID = 1; // This is the user account we're about to delete

                $Action = "deleteUser";
                $Meta = [$UserID];

                $NonceValues = SimpleNonce::GenerateNonce($Action, $Meta);
                    
                header("Location: ./deleteUser.php?UserID=".$UserID."&Nonce=".$NonceValues["Nonce"]."&TimeStamp=".$NonceValues["TimeStamp"]);

                ?>

            You could also have used a form instead of a url:

                <html>
                <body>
                    <?php
                    $UserID = 1; // This is the user account we're about to delete

                    $Action = "deleteUser";
                    $Meta = [$UserID];

                    $NonceValues = SimpleNonce::GenerateNonce($Action, $Meta);
                    ?>

                    <form action="deleteUser.php" method="POST">
                        <input type="hidden" name="UserID" value="<?php print $UserID; ?>">
                        <input type="hidden" name="Nonce" value="<?php print $NonceValues["Nonce"]; ?>">
                        <input type="hidden" name="TimeStamp" value="<?php print $NonceValues["TimeStamp"]; ?>">

                        <input type="submit" value="Delete User">
                    </form>
                </body>
                </html>

        

    Verify Nonce: 

    The VerifyNonce function takes 4 arguments:

    Arg1: InputNonce
    Arg2: Action
    Arg3: TimeStamp
    Arg4: MetaData Array 
    
    The InputNonce is the generated nonce from the GenerateNonce function.
    The Action is the same action (case and spaces etc are important) as used in GenerateNonce.
    The TimeStamp is the generated time stamp from the GenerateNonce function.
    The MetaData array is the same meta data array passed into the GenerateNonce function.

    Continuing with our example of deleting a user account...

    In the page deleteUser.php we might have something like this:

    <?php
    
        $Nonce = filter_var($_GET["Nonce"], FILTER_SANITIZE_STRING);
        $TimeStamp = filter_var($_GET["TimeStamp"], FILTER_SANITIZE_STRING);
        $UserID = intVal($_GET["userID"]);

        $MetaData = [$UserID];
        $Action = "deleteUser";

        // now lets verify the nonce:

        $Result = SimpleNonce::VerifyNonce($Nonce, $Action, $TimeStamp, $MetaData);
        
        if( ! $Result )
        {
            echo "Nonce failed";
            exit();
        }

        echo "Nonce passed, continue....";
    ?>



Take a look at the examples folder for an example of each.
    

Questions:
    Q: Why do we need to pass the timestamp?
    A: The way most nonces are implemented is that when they are created the nonce and the time out is stored in a file system or in a db. When the nonce is used the validity of the nonce and its time out is read from a DB. 
    
    This nonce system does not store nonces in a DB and does not store the unused nonces in the file system. The reason we don't do that is because on busy sites that could cause quite a lot of overhead. For instance, thing about a busy store where you want to list many products on a page to an admin user. Each item in the list might have an edit button as well as a delete button. Each of these buttons require its own nonce. If we list 50 items per page, that's 100 nonces we're writing to a db which may never be used.

    Our system rather works out the nonce on generating it and then reworks it out on verifying it, based on the action, timestamp and meta data. Because we're not prestoring, we need to pass the timestamp!

    Q: If we're passing the time stamp as plain text with a link, what prevents a bad user from altering the time stamp in the URL?
    A: The GenerateNonce function uses the time stamp as part of the nonce field. So even though a bad user can see the time stamp they can't alter it. If they try to alter it the VerifyNonce function will fail.

    Q: If it doesn't write to a DB how does it know that the nonce has only been used once?
    A: We do write a text file. The text file is named with the nonce key. When a nonce is being verified we check that that file does not exist. If it does exist then the nonce has already been used and the nonce fails.

    