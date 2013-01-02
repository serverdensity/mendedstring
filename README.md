serverdensity/mendedstring
==========================

A PHP class for detecting unicode strings with single byte encoded UTF-8 characters in them,
for example like those created by using PHP's `utf8_encode()` function on a string with non-ASCII
characters in it and then saving to a data source that supports full UTF-8 encoding, like MongoDB.

[![Build Status](https://travis-ci.org/serverdensity/mendedstring.png?branch=master)](https://travis-ci.org/serverdensity/mendedstring)

Use case
--------

We created *MendedString* due to our reliance in legacy code on the PHP [utf8_encode](http://php.net/utf8_encode) and [utf8_decode](http://php.net/utf8_decode) builtin functions. Whenever we saved user data to our datastore (in this case MongoDB) we would have to `utf8_encode()` it first and remember to `utf8_decode()` it on output back to the user.

With correct encodings in PHP and the browser set this shouldn't be required, what with strings in MongoDB being stored in the BSON document format which uses UTF-8, this shouldn't be necessary. While migrating our code to use the correct encodings we found that we lots of data already stored in Mongo using single-byte armouring of multi-byte characters (via `utf8_encode`) and to be able to ditch all such armouring we'd need to first migrate the existing data.

*MendedString* does this by checking for these characters and decoding them appropriately, multiple times if needed, until you have a native PHP string encoded with unicode.

**Note**: it doesn't use `mb_detect_encoding` or it's ilk from the multibyte extension as we found this unreliable for detecting single-byte armoured characters, only actual multibyte characters which was useless for our purposes.


Installation
------------

If you're using [Composer](http://getcomposer.org/) for your project you can just add `serverdensity/mendedstring` to your requirements.

Otherwise you'll need to include/require `mendedstring/src/ServerDensity/MendedString.php` in your code.

Usage
-----

To fix a broken (or possibly broken) string with unicode characters in just pass the string into a new `\ServerDensity\MendedString` instance.
Each instance is immutable, so to fix a new string you need to create a new instance, e.g.:

```php
use \ServerDensity\MendedString;

$broken = utf8_encode('hello world' . utf8_encode('«ταБЬℓσ»'));
$mended = new MendedString($broken);

// Mended strings are lazy-converted, you either have to called ->getConverted()
// or use it as a string (e.g. cast it, concat it with another string etc.) like so:
echo (string)$mended;
```

License
-------

*MendedString* is BSD license, feel free to use and abuse, but keep the `LICENSE` file intact.
