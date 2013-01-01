<?php
// ex: set tabstop=4 shiftwidth=4 expandtab fileencoding=utf-8:

class MendedStringTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->plainString = 'hello world ';
        $this->utf8String = '«ταБЬℓσ»';
    }

    public function testInvalidValues()
    {
        $failed = 0;
        try {
            new \ServerDensity\MendedString(null);
        }
        catch (InvalidArgumentException $e)
        {
            $failed++;
        }
        try {
            new \ServerDensity\MendedString(1);
        }
        catch (InvalidArgumentException $e)
        {
            $failed++;
        }
        try {
            new \ServerDensity\MendedString(false);
        }
        catch (InvalidArgumentException $e)
        {
            $failed++;
        }
        try {
            new \ServerDensity\MendedString(1.0);
        }
        catch (InvalidArgumentException $e)
        {
            $failed++;
        }
        $this->assertEquals(4, $failed);
    }

    public function testAsciiString()
    {
        $mended = new \ServerDensity\MendedString($this->plainString);
        $this->assertEquals($this->plainString, (string)$mended);
    }

    public function testUtf8String()
    {
        $decoded = $this->plainString . $this->utf8String;

        $mended = new \ServerDensity\MendedString($decoded);

        $this->assertEquals($decoded, (string)$mended);
    }

    public function testEncodedUtf8String()
    {
        $decoded = $this->plainString . $this->utf8String;
        $encoded = utf8_encode($decoded);

        $mended = new \ServerDensity\MendedString($encoded);

        $this->assertEquals($decoded, (string)$mended);
        $this->assertNotEquals($encoded, (string)$mended);
    }

    public function testMultipleEncodedUtf8Strings()
    {
        $decoded = $this->plainString . $this->utf8String;
        $encoded = utf8_encode($decoded);
        $decoded .= $this->utf8String;
        $encoded .= utf8_encode($this->utf8String);
        $encoded = utf8_encode($encoded);

        $mended = new \ServerDensity\MendedString($encoded);

        $this->assertEquals($decoded, (string)$mended);
        $this->assertNotEquals($encoded, (string)$mended);
    }
}
