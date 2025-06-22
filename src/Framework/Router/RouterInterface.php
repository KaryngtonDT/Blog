<?php

namespace App\Framework\Router;

use PhpDevCommunity\Route;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function get(string $name, string $path, $handler, array $wheres = []): void;

    public function post(string $name, string $path, $handler, array $wheres = []): void;

    public function delete(string $name, string $path, $handler, array $wheres = []): void;

    public function crud($prefixName, $prefixPath, $handler);

    public function match(ServerRequestInterface $serverRequest): ?Route;

    public function generateUri(string $name, array $parameters = []): string;

    public function has(string $name): bool;
}
