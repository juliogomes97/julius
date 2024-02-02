<?php declare(strict_types=1);

namespace Julius\Framework\Http\Interface;

interface RequestInterface
{
    public function getUri() : string;

    public function getMethod() : string;

    public function getPost(string $key, mixed $default = '') : mixed;

    public function getQuery(string $key, mixed $default = '') : mixed;
}