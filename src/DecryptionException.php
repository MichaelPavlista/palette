<?php

declare(strict_types = 1);

namespace Palette;

use Throwable;

class DecryptionException extends Exception
{

    public function __construct()
    {
        parent::__construct('Decryption of query string failed');
    }

}