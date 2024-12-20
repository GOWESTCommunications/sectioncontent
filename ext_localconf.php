<?php
declare(strict_types=1);


use GOWEST\Sectioncontent\Controller\TeaserController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'Sectioncontent',
    'Pi1',
    [
        TeaserController::class => 'index'
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['Sectioncontent_PluginListTypeToCTypeUpdate'] = \GOWEST\Sectioncontent\Upgrades\PluginListTypeToCTypeUpdate::class;
