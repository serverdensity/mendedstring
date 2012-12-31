MendedString
============

PHP library for detecting unicode strings with single byte encoded UTF-8 characters in them,
for example like those created by using PHP's `utf8_encode()` function on a string with non-ASCII
characters in it and then saving to a data source that supports full UTF-8 encoding, like MongoDB.
