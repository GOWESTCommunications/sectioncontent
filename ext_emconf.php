<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "sectioncontent".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Sectioncontent (with Fluid)',
    'description' => 'Create page teasers with data from page properties and its content elements. ' .
        'Based on Extbase and Fluid Template Engine.',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '10.1.11',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 0,
    'lockType' => '',
    'author' => 'Michael Nußbaumer',
    'author_email' => 'm.nussbaumer@go-west.at',
    'author_company' => '',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.0.0-10.4.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    'suggests' => array()
);
