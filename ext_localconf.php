<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sectioncontent']);
$actionNotToCache = '';
if ($extConfiguration['ENABLECACHE'] == '0') {
    $actionNotToCache = 'index';
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GoWest.sectioncontent',
    'Pi1',
    [
        'Teaser' => 'index',
    ],
    [
        'Teaser' => $actionNotToCache,
    ]
);

// Constants
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('sectioncontent', 'constants', ' <INCLUDE_TYPOSCRIPT: source="FILE:EXT:sectioncontent/Configuration/TypoScript/constants.typoscript">');

// Setup     
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('sectioncontent', 'setup', ' <INCLUDE_TYPOSCRIPT: source="FILE:EXT:sectioncontent/Configuration/TypoScript/setup.typoscript">');
