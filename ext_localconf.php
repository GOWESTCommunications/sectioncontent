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
    []
);