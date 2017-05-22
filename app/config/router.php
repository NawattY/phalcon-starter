<?php

$router = $di->getRouter();

$router->removeExtraSlashes(true);

$router->setDefaults([
    'controller' => 'error',
    'action' => 'page404'
]);

// Define your routes here
$router->addGet('/notifications', [
    'controller' => 'notification',
    'action' => 'index'
]);

$router->handle();