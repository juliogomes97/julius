<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use \Julius\Framework\Http\Request;
use \Julius\Framework\Routing\Router;

Router::boot(new Request);

// Landing page route
Router::get('/b-b', [\Julius\Test\Controllers\LandingController::class, 'index']);

// Dashboard routes
Router::group('/dashboard', function()
{
    Router::get('/', [\Julius\Test\Controllers\Dashboard\DashboardController::class, 'index']);

    Router::group('posts', function()
    {
        Router::get('/', [\Julius\Test\Controllers\Dashboard\PostsController::class, 'index']);
    });

    Router::group('settings', function()
    {
        Router::get('/',        [\Julius\Test\Controllers\Dashboard\Settings\SettingsController::class, 'index']);
        Router::get('/account', [\Julius\Test\Controllers\Dashboard\Settings\AccoutController::class, 'index']);
        Router::get('/groups',  [\Julius\Test\Controllers\Dashboard\Settings\GroupsController::class, 'index']);
    });
});

// User routes -> id only number
Router::group('/user/:id', function()
{
    Router::get('/', [\Julius\Test\Controllers\User\UserController::class, 'index'], [
        'id' => '([0-9]+)'
    ]);
    Router::get('/profile', [\Julius\Test\Controllers\User\ProfileController::class, 'index'], [
        'id' => '([0-9]+)'
    ]);
});

Router::add('POST', '/user/list', [\Julius\Test\Controllers\UsersListController::class, 'index']);

// Exception route
Router::fallback([\Julius\Test\Controllers\NotFoundController::class, 'index']);
