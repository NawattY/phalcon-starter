<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    $environment = environment();
    $mainConfig = require APP_PATH.'/config/config.php';
    $envConfig = require APP_PATH . "/config/config_{$environment}.php";
    $mainConfig->merge($envConfig);
    return $mainConfig;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Makro Custom
 */
function stringIs($pattern, $value)
{
    if ($pattern == $value) {
        return true;
    }

    $pattern = preg_quote($pattern, '#');

    // Asterisks are translated into zero-or-more regular expression wildcards
    // to make it convenient to check if the strings starts with the given
    // pattern such as "library/*", making any string check convenient.
    $pattern = str_replace('\*', '.*', $pattern);

    return (bool) preg_match('#^'.$pattern.'\z#u', $value);
}

function environment()
{
    $environments = array(
        'local' => array('*.loc', 'localhost', '*.app', 'scotchbox', '*.vagr', '*.dock', '*.dev', '*.local'),
        'develop'    => array('*.igetapp.com', 'dep-code.igetapp.com'),
        'staging'    => array('staging.makroclick.com', '*.eggdigital.com'),
        'production' => array('*.com')
    );

    $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname();

    foreach ($environments as $environment => $hosts) {
        foreach ((array) $hosts as $host) {
            if (stringIs($host, $hostname)) {
                return $environment;
            }
        }
    }

    return 'production';
}

$di->set('dispatcher', function () {
    $eventsManager = new Phalcon\Events\Manager();

    $eventsManager->attach('dispatch', function ($event, $dispatcher, $exception) {
        if ($event->getType() == 'beforeNotFoundAction') {
            $dispatcher->forward([
                // 'namespace' => 'App\Controllers',
                'controller' => 'error',
                'action' => 'page404'
            ]);

            return false;
        }

        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        // 'namespace' => 'App\Controllers',
                        'controller' => 'error',
                        'action' => 'page404'
                    ]);
                    return false;
            }
        }
    });

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

$di->set('response', function () {
    $response = new \Phalcon\Http\Response();
    return $response;
});

$di->set('request', function () {
    $request = new \Phalcon\Http\Request();
    return $request;
});

$di->setShared('mongo', function () {
    $config = $this->getConfig();
    $mongo = new \Phalcon\Db\Adapter\MongoDB\Client("mongodb://{$config->mongodb->host}:{$config->mongodb->port}");
    return $mongo->selectDatabase($config->mongodb->dbname);
});

$di->set('collectionManager', function () {
    return new Phalcon\Mvc\Collection\Manager();
});

//$di->set(
//    'mongo',
//    function () {
//        $config = $this->getConfig();
//        $mongo = new MongoClient("mongodb://{$config->mongodb->host}:{$config->mongodb->port}");
//        return $mongo->selectDB($config->mongodb->dbname);
//    },
//    true
//);