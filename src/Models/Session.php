<?php declare(strict_types=1);

namespace Julius\Framework\Models;

class Session
{
    public function __construct()
    {
        if (!isset($_SESSION))
        {
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

    public function is_logged() : bool
    {
        return !empty($this->get('user'));
    }

    public function is_valid_CSRF(string $csrf) : bool
    {
        return hash_equals($_SESSION['csrf'], $csrf);
    }
}