<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * @var Laravel\Lumen\Routing\Router $router
 */

$router->get('/', function () use ($router) {
    return view('videos');
});

$router->get('get-all-videos', 'VideoController@getAll');
$router->get('search-video-by-tag', 'VideoController@searchByTag');
