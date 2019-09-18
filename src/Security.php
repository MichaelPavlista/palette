<?php

namespace Palette\Utils;

use InvalidArgumentException;
use Palette\SecurityException;

/**
 * Class Security
 * @package Palette\Utils
 */
class Security
{
    /**
     * Create signed palette query.
     * @param string $paletteQuery
     * @param string $signingKey
     * @return string
     */
    public static function signPaletteQuery($paletteQuery, $signingKey)
    {
        if(!is_string($paletteQuery) || !is_string($signingKey) || !$paletteQuery || !$signingKey)
        {
            throw new InvalidArgumentException('Parameters $paletteQuery and $signingKey must be non-empty string');
        }

        $paletteQuerySignatute = hash_hmac('sha256', $paletteQuery, $signingKey);

        // JSON string with palette query and signature.
        $signedString = json_encode([$paletteQuery, $paletteQuerySignatute]);

        // URL safe base64_encode.
        return rtrim(strtr(base64_encode($signedString), '+/', '-_'), '=');
    }


    /**
     * @param string $signedPaletteQuery
     * @param string $signingKey
     * @return string
     * @throws SecurityException
     */
    public static function validateSignedPaletteQuery($signedPaletteQuery, $signingKey)
    {
        if(!is_string($signingKey) || !$signingKey)
        {
            throw new SecurityException('Signing key must be non-empty string');
        }

        if(!is_string($signedPaletteQuery) || !$signedPaletteQuery)
        {
            throw new SecurityException('Signed palette query must be non-empty string');
        }

        // URL safe base64_decode
        $signedString = base64_decode(str_pad(strtr($signedPaletteQuery, '-_', '+/'), strlen($signedPaletteQuery) % 4, '=', STR_PAD_RIGHT));

        if($signedString === FALSE || !is_string($signedString))
        {
            throw new SecurityException('Signed palette query is not valid base64 string');
        }

        // JSON decode signed string
        $params = json_decode($signedString, TRUE);

        if(json_last_error() !== JSON_ERROR_NONE || !is_array($params) || count($params) !== 2)
        {
            throw new SecurityException('Signed palette query contains invalid json data');
        }

        // Check palette query and signature param values.
        list($paletteQuery, $paletteQuerySignature) = $params;

        if(!is_string($paletteQuery) || !is_string($paletteQuerySignature) || !$paletteQuerySignature)
        {
            throw new SecurityException('Signed palette query contains invalid params');
        }

        // Check palette query signature.
        $calculatedSignature = hash_hmac('sha256', $paletteQuery, $signingKey);

        if($calculatedSignature !== $paletteQuerySignature)
        {
            throw new SecurityException('Invalid signed palette query signature');
        }

        return $paletteQuery;
    }
}
