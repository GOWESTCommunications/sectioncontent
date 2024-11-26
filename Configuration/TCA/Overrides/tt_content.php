<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function (): void {

    $pluginSignature = ExtensionUtility::registerPlugin(
        'Sectioncontent',
        'Pi1',
        'Sectioncontent'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['columnsOverrides'] = [
        'bodytext' => [
            'config' => [
                'type' => 'text',
                'renderType' => 'text',
                'enableRichtext' => true,  // if needed
            ]
        ]
    ];

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,',
        $pluginSignature,
        'after:subheader',
    );

    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:sectioncontent/Configuration/FlexForms/flexform_teaser.xml',
        $pluginSignature
    );
})();
