<?php

namespace client;

class Skin
{
    /** @var string */
    private $data;
    /** @var string */
    private $cape;

    /**
     * Skin constructor.
     * @param string|null $skinSrc
     * @param string|null $capeSrc
     */
    function __construct(string $skinSrc = \null, string $capeSrc = \null)
    {
        $this->data = isset($skinSrc) ? (is_file($skinSrc) ? self::getSkin($skinSrc) : \null) : \null;
        $this->cape = isset($capeSrc) ? (is_file($capeSrc) ? self::getCape($capeSrc) : \null) : \null;
    }

    /**
     * @return null|string
     */
    function getSkinData()
    {
        return $this->data;
    }

    /**
     * @return null|string
     */
    function getCapeData()
    {
        return $this->cape;
    }

    /**
     * @param string $filename
     * @return string
     */
    static function getSkin(string $filename): string
    {
        $im = imagecreatefrompng($filename);
        list($width, $height) = getimagesize($filename);
        $bytes = "";
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = @imagecolorat($im, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        imagedestroy($im);
        return $bytes;
    }

    /**
     * @param string $filename
     * @param string $bytes
     */
    static function saveSkin(string $filename, string $bytes): void
    {
        $len = strlen($bytes);
        $im = imagecreatetruecolor(64, $len === 8192 ? 32 : 64);
        $x = $y = $part = 0;
        while ($y < 64) {
            $cid = substr($bytes, $part, 3);
            if (isset($cid[0])) {
                imagesetpixel($im, $x, $y, imagecolorallocate($im, ord($cid[0]), ord($cid[1]), ord($cid[2])));
            }
            $x++;
            $part += 4;
            if ($x === 64) {
                $x = 0;
                $y++;
            }
        }
        imagepng($im, $filename);
        imagedestroy($im);
    }

    /**
     * @param string $filename
     * @return string
     */
    static function getCape(string $filename): string
    {
        $im = imagecreatefrompng($filename);
        list($width, $height) = getimagesize($filename);
        $bytes = "";
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $argb = @imagecolorat($im, $x, $y);
                $a = ((~((int)($argb >> 24))) << 1) & 0xff;
                $r = ($argb >> 16) & 0xff;
                $g = ($argb >> 8) & 0xff;
                $b = $argb & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        imagedestroy($im);
        return $bytes;
    }
}