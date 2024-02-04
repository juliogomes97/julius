<?php declare(strict_types=1);

namespace Julius\Framework\Http;

final class HttpHandler
{
    public const STATUS_CONTINUE                = 100;
    public const STATUS_SWITCHING_PROTOCOLS     = 101;
    public const STATUS_OK                      = 200;
    public const STATUS_CREATED                 = 201;
    public const STATUS_NO_CONTENT              = 204;
    public const STATUS_MOVED_PERMANENTLY       = 301;
    public const STATUS_MOVED_TEMPORARILY       = 302;
    public const STATUS_NOT_MODIFIED            = 304;
    public const STATUS_BAD_REQUEST             = 400;
    public const STATUS_UNAUTHORIZED            = 401;
    public const STATUS_FORBIDDEN               = 403;
    public const STATUS_NOT_FOUND               = 404;
    public const STATUS_METHOD_NOT_ALLOWED      = 405;
    public const STATUS_INTERNAL_SERVER_ERROR   = 500;
    public const STATUS_BAD_GATEWAY             = 502;
    public const STATUS_SERVICE_UNAVAILABLE     = 503;

    private int     $statusCode;
    private array   $headers;

    public function __construct(array $headers = [])
    {
        $this->addHeader('Content-Type', 'text/plain');
        $this->addHeader('Strict-Transport-Security', 'max-age=63072000;');
        $this->addHeader('Cache-Control', 'max-age=604800, must-revalidate');
        
        foreach($headers as $key => $value)
        {
            $this->addHeader($key, $value);
        }

        $this->statusCode = self::STATUS_OK;
    }

    public function addHeader(string $headerName, string $headerValue) : void
    {
        $this->headers[$headerName] = $headerValue;
    }

    public function setStatusCode(int $statusCode) : void
    {
        $this->statusCode = $statusCode;
    }

    public function defineHeaders(bool $replace = true) : void
    {
        http_response_code($this->statusCode);
        
        foreach($this->headers as $key => $value)
        {
            header($key .': '. $value, $replace);
        }
    }

    public function getHeader(string $headerName) : array
    {
        $headers = [];

        foreach($this->headers as $key => $value)
        {
            if(strcasecmp($key, $headerName))
            {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }
}