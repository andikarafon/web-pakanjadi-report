<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('oracle-test', 'OracleTest::index');
$routes->get('oracle-model-test', 'OracleModelTest::index');
