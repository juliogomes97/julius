<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Julius\Framework\Routing\Router;

$router = new Router;

$router->get('/', \Julius\Test\Controllers\LandingController::class);

$router->group('dashboard', function(Router $router)
{
    // Controlador por defeito ao acessar Uri => /dashboard
    $router->get('',        \Julius\Test\Controllers\Dashboard\DashboardController::class);

    $router->group('posts', function(Router $router)
    {
        $router->get('/',   \Julius\Test\Controllers\Dashboard\PostsController::class);
    });

    $router->group('settings', function(Router $router)
    {
        // Controlador por defeito ao acessar Uri => /dashboard/settings
        $router->get('/',            \Julius\Test\Controllers\Dashboard\Settings\SettingsController::class);
        
        // Uri => /dashboard/settings/account
        $router->get('account',     \Julius\Test\Controllers\Dashboard\Settings\AccoutController::class);
        // Uri => /dashboard/settings/groups
        $router->get('groups',      \Julius\Test\Controllers\Dashboard\Settings\GroupsController::class);
    });
});

$router->group('user/:id', function(Router $router)
{
    // :id só aceita numeros
    $regex = [':id' => '([0-9]+)'];

    // Controlador por defeito ao acessar Uri => /user/{0-9}
    $router->get('/',            \Julius\Test\Controllers\User\UserController::class, $regex);
    // Uri => /user/{0-9}/profile
    $router->get('profile',     \Julius\Test\Controllers\User\ProfileController::class, $regex);

    $router->group('settings', function(Router $router)
    {
        // :id tem que ser igual a 'session'
        $regex = [':id' => 'session'];

        // Controlador por defeito ao acessar Uri => /user/session/settings
        $router->get('/', \Julius\Test\Controllers\User\Settings\SettingsController::class, $regex);
        
        $router->group(':hello/privacy', function(Router $router)
        {
            // :id tem que ser igual a 'none'
            // :hello tem que ser igual 'world'
            $regex = [':id' => 'none', ':hello' => 'world'];

            // Controlador por defeito ao acessar Uri => /user/none/settings/world/privacy
            $router->get('/', \Julius\Test\Controllers\User\Settings\PrivacyController::class, $regex);
        });
    });

});


$router->get('user/test', \Julius\Test\Controllers\UsersListController::class);

$router->fallback(\Julius\Test\Controllers\NotFoundController::class);

//var_dump($request);