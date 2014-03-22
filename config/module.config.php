<?php
return array(
    'router' => array(
        'routes' => array(
            'zf2-instrument-module' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/zf2-module-composer/',
                    'defaults' => array(
                        'controller' => 'Zf2ModuleComposer\Controller\IndexController',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // Segment route for UI (manual management)
                    'ui' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'ui/[:controller][/:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z0-9_-]+',
                                'action' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Zf2ModuleComposer\Controller',
                                'controller' => 'Index',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    // Segment route for Service (automated management)
                    'service' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'service/[:controller][/:action]',
                            'constraints' => array(
                                '__NAMESPACE__' => 'Zf2ModuleComposer\Controller\Service',
                                'controller' => '[a-zA-Z0-9_-]+',
                                'action' => '[a-zA-Z0-9_-]+'
                            ),
                        ),
                    ),
                )
            )
        ),
    ),
);