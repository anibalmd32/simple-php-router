<?php

namespace SimplePhpRouter\Attributes;

use Attribute;
use SimplePhpRouter\Attributes\Route;

#[Attribute(Attribute::TARGET_METHOD)]

final class Patch extends Route
{
    public function __construct(public string $path)
    {
        parent::__construct($path, 'PATCH');
    }
}
