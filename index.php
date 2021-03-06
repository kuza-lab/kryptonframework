<?php

use Kuza\Krypton\Exceptions\UnauthenticatedException;
use Kuza\Krypton\Exceptions\UnauthorizedException;
use Kuza\Krypton\Framework\JsonResponse;

//require the composer vendor libraries autoloader
require "bootstrap.php";

try {

    // set cors

    $cors = [
        "Access-Control-Allow-Origin: *",
        "Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS",
        "Access-Control-Allow-Headers: X-Requested-With , Content-Type , Authorization, Business, Role, Region, Country",
        "Access-Control-Max-Age: 5"
    ];

    $app->run($cors);

} catch (Exception $ex) {

    $response = new JsonResponse();

    if ($ex instanceof UnauthenticatedException) {
        $code = 401;
    } elseif ($ex instanceof UnauthorizedException) {
        $code = 403;
    } else {
        $code = 500;
    }

    if ($app->requests->isJsonRequest()) {

        $response->message = $ex->getMessage();

        if ($app->requests->backtrace == 1) {

            $response->errors = $ex->getTrace();
        }

        $app->response->status_code($code)->json($response->toArray());

    } else {
        // web access. redirect to error pages
        $message = $ex->getMessage();
        $trace = $ex->getTrace();

        $app->view($code, [], compact('message', 'trace'));
    }

    $app->view($code, [], compact('message', 'trace'));

}