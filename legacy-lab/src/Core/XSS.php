<?php
declare(strict_types=1);
namespace LegacyLab\Core;

class XSS
{
    public function __construct(
        private readonly bool $xssProtected = true
    ) {}
    
    public function output(string $input): string
    {
        return $this->xssProtected
            ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8')
            : $input;
    }
}