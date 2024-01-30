<?php declare(strict_types=1);

namespace Julius\Framework\Models;

class Session
{
    public function __construct(array $session_params = [])
    {
        if (!isset($_SESSION))
        {
            session_set_cookie_params(array_merge([
                    'lifetime'  => 86400, // 1 dia
                    'path'      => '/',
                    'secure'    => true,
                    'httponly'  => true,
                    'samesite'  => 'Strict'
                ], 
                $session_params)
            );

            session_start();
        }

        if (empty($_SESSION['csrf']))
        {
            if (function_exists('random_bytes'))
            {
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
            }
            else
            {
                $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
        }
    }

    public function login(mixed $data) : void
    {
        $this->set('user', $data);
    }

    public function logout() : void
    {
        $this->delete('user');

        session_destroy();
    }

    public function delete(string $key) : void
    {
        unset($_SESSION[$key]);
    }

    public function set(string $key, mixed $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) : mixed
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
    }

    public function isAuthenticated() : bool
    {
        return !empty($this->get('user'));
    }

    public function isValidCSRF(string $csrf) : bool
    {
        return hash_equals($_SESSION['csrf'], $csrf);
    }
}