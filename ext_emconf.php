<?php

/**
 * Extension Manager/Repository config file for ext "sectioncontent".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Sectioncontent',
    'description' => 'Create page teasers with data from page properties and its content elements.',
    'author' => 'Michael Nußbaumer',
    'author_email' => 'm.nussbaumer@go-west.at',
    'author_company' => 'GO.WEST Communications GmbH',
    'version' => '12.0.2',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'GOWEST\\Sectioncontent\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
];
