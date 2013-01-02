<?php
// ex: set tabstop=4 shiftwidth=4 expandtab:

namespace ServerDensity\MendedString;

class MendedString
{
    protected $_original;
    protected $_converted;

    /**
     * Constructor for MendedString, given a character containing single byte encoded
     * multi byte characters, give a correct UTF-8 string value.
     * Usage:
     * ```
     * $mended = new MendedString($brokenString);
     * echo (string)$mended;
     * ```
     *
     * @param string $original
     */
    public function __construct($original)
    {
        if (!is_string($original))
        {
            throw new \InvalidArgumentException('$original should be a string, received "' . $original . '" of type "' . gettype($original) . '"');
        }

        $this->_original = $original;
    }

    /**
     * Detect encoded UTF-8 characters within a given string
     * regex pilfered from http://stackoverflow.com/a/1037368
     * @param string $string
     * @return bool
     */
    public static function detectEncodedUtf8Chararacters($string)
    {
        return (bool)preg_match(
            '%(?:
                [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
                |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )+%xs', 
            $string);
    }

    /**
     * Convert a single byte (or ASCII) string with encoded multi byte characters (like UTF-8)
     * in it (as created by running through the utf8_encode() PHP built-in) into a fully decoded
     * UTF-8 string
     * @param string $original
     * @return string
     */
    public static function convert($original)
    {
        $decoded = utf8_decode($original);

        if ($decoded == $original || !self::detectEncodedUtf8Chararacters($decoded))
        {
            // Either only ASCII chars here or is already a proper UTF string
            return $original;
        }

        // Account for multiple-encoded strings
        $decoded2 = $decoded;
        while (true)
        {
            $decoded2 = utf8_decode($decoded);
            if (!self::detectEncodedUtf8Chararacters($decoded2))
            {
                break;
            }
            $decoded = $decoded2;
        }

        return $decoded;
    }

    /**
     * Lazy loaded accessor for converted UTF-8 string, runs converted() if not already run.
     * @return string
     */
    public function getConverted()
    {
        if ($this->_converted === null)
        {
            $this->_converted = self::convert($this->_original);
        }
        return $this->_converted;
    }

    public function __toString()
    {
        return $this->getConverted();
    }

    public function __get($name)
    {
        if ($name === 'original')
        {
            return $this->_original;
        }
        else if ($name === 'converted')
        {
            return $this->getConverted();
        }
    }
}
