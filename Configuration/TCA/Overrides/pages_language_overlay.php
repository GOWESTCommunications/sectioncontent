<?php
defined('TYPO3_MODE') or die();

$temporaryColumnsNames = array(
    '--linebreak--',
    'tx_sectioncontent_abstract_title',
    '--linebreak--',
    'tx_sectioncontent_abstract_subtitle',
    '--linebreak--',
    'tx_sectioncontent_abstract_description',
    '--linebreak--',
    'tx_sectioncontent_abstract_image',
    '--linebreak--',
    'tx_sectioncontent_abstract_image_2',
    '--linebreak--',
    'tx_sectioncontent_abstract_image_3',
    '--linebreak--',
    'tx_sectioncontent_abstract_image_4',
    '--linebreak--',
    'tx_sectioncontent_abstract_reference_url',
);


$temporaryColumns = array(
        'tx_sectioncontent_abstract_title' => array(
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_title',
            'l10n_cat' => 'text',
            'config' => array(
                'type' => 'input',
                'size' => '50',
                'max' => '255',
                'eval' => 'trim'
            )
        ),
        'tx_sectioncontent_abstract_subtitle' => array(
            'exclude' => 1,
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_subtitle',
            'config' => array(
                'type' => 'input',
                'size' => '50',
                'max' => '255',
                'eval' => 'trim'
            )
        ),
        'tx_sectioncontent_abstract_description' => array(
            'exclude' => 1,
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_description',
            'config' => array(
                'type' => 'text',
                'size' => '50',
                'cols' => '40',
                'rows' => '3',
                'eval' => 'trim'
            )
        ),
        
        'tx_sectioncontent_abstract_image' => array(
            'exclude' => 1,
            //'l10n_mode' => 'mergeIfNotBlank',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_sectioncontent_abstract_image', array (
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords'  => true,
                        'showSynchronizationLink'         => true,
                        'showAllLocalizationLink'         => false,
                        
                        
                    ),
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                        ],
                    ],
                ),
                'jpg,jpeg,png'
            ),
        ),
        'tx_sectioncontent_abstract_image_2' => array(
            'exclude' => 1,
            //'l10n_mode' => 'mergeIfNotBlank',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_image_2',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_sectioncontent_abstract_image_2', array (
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords'  => true,
                        'showSynchronizationLink'         => true,
                        'showAllLocalizationLink'         => false,
                        
                        
                    ),
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                        ],
                    ],
                ),
                'jpg,jpeg,png'
            ),
        ),
        'tx_sectioncontent_abstract_image_3' => array(
            'exclude' => 1,
            //'l10n_mode' => 'mergeIfNotBlank',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_image_3',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_sectioncontent_abstract_image_3', array (
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords'  => true,
                        'showSynchronizationLink'         => true,
                        'showAllLocalizationLink'         => false,
                        
                        
                    ),
                    'minitems' => 0,
                    'maxitems' => 1,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                        ],
                    ],
                ),
                'jpg,jpeg,png'
            ),
        ),
        'tx_sectioncontent_abstract_image_4' => array(
            'exclude' => 1,
            //'l10n_mode' => 'mergeIfNotBlank',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_image_4',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'tx_sectioncontent_abstract_image_4', array (
                    'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords'  => true,
                        'showSynchronizationLink'         => true,
                        'showAllLocalizationLink'         => false,
                        
                        
                    ),
                    'minitems' => 0,
                    'maxitems' => 1,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                                    --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                    --palette--;;filePalette'
                            ],
                        ],
                    ],
                ),
                'jpg,jpeg,png'
            ),
        ),
        'tx_sectioncontent_abstract_reference_url' => array(
            'exclude' => 1,
            //'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_reference_url',
            'config' => array(
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 50,
                'max' => 1024,
                'eval' => 'trim',
                'softref' => 'typolink',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            )
        ),
);


for ($count = 1; $count <= 8; $count++) {
    $temporaryColumnsNames[] = '--linebreak--';
    $temporaryColumnsNames[] = 'tx_sectioncontent_abstract_attr_' . $count;
    $temporaryColumns['tx_sectioncontent_abstract_attr_' . $count] = array(
        'exclude' => 1,
        //'l10n_mode' => 'mergeIfNotBlank',
        'l10n_cat' => 'text',
        'label' => 'LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_abstract_attr_' . $count,
        'config' => array(
            'type' => 'input',
            'size' => '50',
            'eval' => 'trim',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        )
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'pages_language_overlay',
        $temporaryColumns
);


 
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages_language_overlay',
    '--div--;LLL:EXT:sectioncontent/Resources/Private/Language/locallang_db.xlf:tx_sectioncontent_new_tab,' . implode(',', $temporaryColumnsNames)
);