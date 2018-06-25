# :package_name

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

# SimpleNonce
A simple Nonce implementation in PHP

This is a very simple nonce implementation.  Uses PSR-2 and PSR-4

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```       
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require softsmart/simple-nonce
```

## Usage

``` php
// Generate Nonce
$userID = 1; // This is the user account we're about to delete

$action = "deleteUser";
$meta = [$UserID];

$nonceValues = SimpleNonce::GenerateNonce($action, $meta);
header("Location: ./deleteUser.php?userID=".$userID."&nonce=".$nonceValues["nonce"]."&timeStamp=".$nonceValues["timeStamp"]);


// Verify Nonce
$userID = 1; // This is the user account we're about to delete

$action = "deleteUser";
$meta = [$UserID];

$result = SimpleNonce::VerifyNonce($nonceValues["nonce"], $action, $nonceValues["timeStamp"], $meta);

if( ! $Result )
{
    echo "Nonce failed";
    exit();
}

echo "Nonce passed, continue....";
    
```


## Testing

``` bash
$ phpcs -c phpunit.xml
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.


## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

GNU GENERAL PUBLIC LICENSE. Please see [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/aur/license/yaourt.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/jsmcm
[link-contributors]: ../../contributors


Questions:

Q: Why do we need to pass the timestamp?

A: The way most nonces are implemented is that when they are created the nonce and the time out is stored in a file system or in a db, redis, etc. When the nonce is used the validity of the nonce and its time out is read from a DB. 

This nonce system does not store nonces in a DB and does not store the unused nonces in the file system. The reason we don't do that is because on busy sites that could cause quite a lot of overhead. For instance, think about a busy store where you want to list many products on a page to an admin user. Each item in the list might have an edit button as well as a delete button. Each of these buttons require its own nonce. If we list 50 items per page, that's 100 nonces we're writing to a db which may never be used.

Our system rather works out the nonce on generating it and then reworks it out on verifying it, based on the action, timestamp and meta data. Because we're not prestoring, we need to pass the timestamp!


Q: If we're passing the time stamp as plain text with a link, what prevents a bad user from altering the time stamp in the URL?

A: The generateNonce function uses the time stamp as part of the nonce field. So even though a bad user can see the time stamp they can't alter it. If they try to alter it the verifyNonce function will fail.


Q: If it doesn't write to a DB how does it know that the nonce has only been used once?

A: We do write a text file. The text file is named with the nonce key. When a nonce is being verified we check that that file does not exist. If it does exist then the nonce has already been used and the nonce fails.

