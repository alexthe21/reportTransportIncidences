<?php
/**
 * xenFramework (http://xenframework.com/)
 *
 * @link        http://github.com/xenframework for the canonical source repository
 * @copyright   Copyright (c) xenFramework. (http://xenframework.com)
 * @license     Affero GNU Public License - http://en.wikipedia.org/wiki/Affero_General_Public_License
 */

/**
 * IoC
 *
 * In this array you can put your own dependencies
 *
 * **********
 * Important! - Remember to create the properties and the setters methods in your classes to enable dependency injection
 * By default you have this dependency properties already created:
 *      'model' in your controllers
 * **********
 *
 * Each entry is a service with his dependencies declared in an array
 *
 * A service has to have one setter method for each dependency
 *
 * You can use default dependencies as: {Database_#dbName#} which are resolved in xen\BootstrapBase
 * The rest of services and dependencies must have their NAMESPACE as a part of their names
 *
 * array(
 *      'service1' => array(
 *          'dependencySetter1' => 'dependency1',
 *          'dependencySetter2' => 'dependency2',
 *          ...
 *          'dependencySetterN' => 'dependencyN',
 *      ),
 *      'service2' => array(
 *          'dependencySetter1' => 'dependency1',
 *          'dependencySetter2' => 'dependency2',
 *          ...
 *          'dependencySetterN' => 'dependencyN',
 *      ),
 *      ...
 *      'serviceN' => array(
 *          'dependencySetter1' => 'dependency1',
 *          'dependencySetter2' => 'dependency2',
 *          ...
 *          'dependencySetterN' => 'dependencyN',
 *      ),
 * )
 */
return array(
    'models\\ClientsModel'                => array(
        'db'        => 'Database_db1',
    ),
    'models\\AdminsModel'                => array(
        'db'        => 'Database_db1',
    ),
    'controllers\\ClientsController'      => array(
        'clientModel'     => 'models\\ClientsModel',
    ),
    'controllers\\LoginController'      => array(
        'adminModel'     => 'models\\AdminsModel',
        'clientModel'     => 'models\\ClientsModel',
    ),
    'controllers\\AdminController'      => array(
        'adminModel'     => 'models\\AdminsModel',
        'clientModel'     => 'models\\ClientsModel',
    ),
);