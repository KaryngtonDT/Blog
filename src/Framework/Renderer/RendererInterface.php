<?php

namespace App\Framework\Renderer;

interface RendererInterface
{
    public function addPath(string $path, string $namespace): void;

    public function addGlobal(string $name, $value);

    public function render($name, array $context = []): string;
}
