<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
$actionNotToCache = '';
if ($extConfiguration['ENABLECACHE'] == '0') {
    $actionNotToCache = 'index';
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GoWest.' . $_EXTKEY,
    'Pi1',
    
    array(
        'Teaser' => 'index',
    ),
    
    array(
        'Teaser' => $actionNotToCache,
    )
);

$rootLineFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
    ',',
    $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'],
    true
);
$rootLineFields[] = 'sorting';

$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = implode(',', $rootLineFields);
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .= ',tx_sectioncontent_abstract_title, tx_sectioncontent_abstract_subtitle, tx_sectioncontent_abstract_description, tx_sectioncontent_abstract_attr_1, tx_sectioncontent_abstract_attr_2, tx_sectioncontent_abstract_attr_3, tx_sectioncontent_abstract_attr_4, tx_sectioncontent_abstract_attr_5, tx_sectioncontent_abstract_attr_6, tx_sectioncontent_abstract_attr_7, tx_sectioncontent_abstract_attr_8, tx_sectioncontent_abstract_image, tx_sectioncontent_abstract_image_2, tx_sectioncontent_abstract_reference_url';

// Constants
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY,'constants',' <INCLUDE_TYPOSCRIPT: source="FILE:EXT:'. $_EXTKEY .'/Configuration/TypoScript/constants.txt">');

// Setup     
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY,'setup',' <INCLUDE_TYPOSCRIPT: source="FILE:EXT:'. $_EXTKEY .'/Configuration/TypoScript/setup.txt">');