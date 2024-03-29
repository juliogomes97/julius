<?php declare(strict_types=1);

namespace Julius\Framework\Http;

use \Julius\Framework\Http\Interface\RequestInterface;

final class Request implements RequestInterface
{
    private string  $method;
    private string  $uri;
    private array   $post;
    private array   $query;

    public function __construct()
    {
        $this->method   = strtoupper($_SERVER['REQUEST_METHOD']);
        
        $this->uri      = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        $this->post     = $this->filterInput(INPUT_POST);
        $this->query    = $this->filterInput(INPUT_GET);
    }

    public function getUri() : string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPost(string $key, mixed $default = '') : mixed
    {
        return self::filterArray($key, $this->post, $default);
    }

    public function getQuery(string $key, mixed $default = '') : mixed
    {
        return self::filterArray($key, $this->query, $default);
    }

    private static function filterArray(string $key, array $data, mixed $default) : mixed
    {
        return array_key_exists($key, $data) && $data[$key] !== null ? $data[$key] : $default;
    }

    private function filterInput(int $input) : array
    {
        $data = filter_input_array($input);

        if($data == null)
            return [];
        
        $data = array_map('trim', $data);
        $data = array_map('htmlspecialchars', $data);

        return $data;
    }
}