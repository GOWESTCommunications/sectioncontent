<?php

defined('TYPO3') or die();

(static function (): void {

    
    // \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    //     'sectioncontent',
    //     'Pi1',
    //     'Sectioncontent'
    // );

    $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Sectioncontent',
        'Pi1',
        'Sectioncontent'
    );

    
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';


    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:sectioncontent/Configuration/FlexForms/flexform_teaser.xml'
    );
})();