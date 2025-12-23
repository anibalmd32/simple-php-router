<?php

namespace SimplePhpRouter\Attributes;

use Attribute;
use SimplePhpRouter\Attributes\Route;

#[Attribute(Attribute::TARGET_METHOD)]

final class Head extends Route
{
    public function __construct(public string $path)
    {
        parent::__construct($path, 'HEAD');
    }
}
