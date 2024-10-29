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

    
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive,frame_class';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform'; 
    
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
        'bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel',
        'list',
        'after:header'
    );
    

    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:sectioncontent/Configuration/FlexForms/flexform_teaser.xml'
    );
})();
