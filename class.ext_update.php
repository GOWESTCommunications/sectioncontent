<?php

namespace GoWest\Sectioncontent\Controller;

/*
 *  Copyright notice
 *
 *  (c) 2013-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

/**
 * Class for updating the db
 */
class ext_update
{
    /**
     * @var string Name of the extension this controller belongs to
     */
    protected $extensionName = 'Sectioncontent';
    
    
    /**
     * @var array UPDATE Queries
     */
    protected $updateQueries = [
        "UPDATE tt_content SET pi_flexform = replace(pi_flexform, 'view.partialRootPath.10', 'view.partialRootPath.300') WHERE pi_flexform like '%view.partialRootPath.10%' and list_type = 'sectioncontent_pi1' and uid > 0;",
        "UPDATE tt_content SET pi_flexform = replace(pi_flexform, 'view.layoutRootPath.10', 'view.layoutRootPath.300') WHERE pi_flexform like '%view.layoutRootPath.10%' and list_type = 'sectioncontent_pi1' and uid > 0;",
    ];
    
    
    /**
     * @var \PDO
     */
    protected $databaseHandle;

    /**
     * @var ObjectManager Extbase Object Manager
     */
    protected $objectManager;

    /**
     * @var InstallUtility Extension Manager Install Tool
     */
    protected $installTool;

    /**
     * Main function, returning the HTML content
     *
     * @return string HTML
     */
    public function main()
    {
        $content = '';

        // Process the database updates of this base extension (we want to re-process these updates every time the update script is invoked)
        $extensionSitePath =
            ExtensionManagementUtility::extPath(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName));
        $content .= '<p>' . nl2br(LocalizationUtility::translate('updateTables', $this->extensionName)) . '</p>';
        
        // update tt_content set header = "new" where uid = 120;
        $dbConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');
        foreach($this->updateQueries as $updateQuery) {
            $dbConnection->prepare($updateQuery)->execute();
            $content .= '<p>' . nl2br('Execute: ' . $updateQuery) . '</p>';
        }
        
        
        
        
        
        
        
        
        
        
        return $content;
    }

    /**
     * Processes the tables SQL File (ext_tables)
     *
     * @param string $extensionKey
     *
     * @return void
     */
    protected function processDatabaseUpdates($extensionKey)
    {
        $extensionSitePath = ExtensionManagementUtility::extPath($extensionKey);
        $extTablesSqlFile = $extensionSitePath . 'ext_tables.sql';
        $extTablesSqlContent = '';
        if (file_exists($extTablesSqlFile)) {
            $extTablesSqlContent .= GeneralUtility::getUrl($extTablesSqlFile);
        }
        if ($extTablesSqlContent !== '') {
            if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getNumericTypo3Version())
                < 9000000) {
                $this->installTool->updateDbWithExtTablesSql($extTablesSqlContent);
            } else {
                $sqlReader = GeneralUtility::makeInstance(SqlReader::class);
                $schemaMigrator = GeneralUtility::makeInstance(SchemaMigrator::class);
                $sqlStatements = [];
                $sqlStatements[] = $extTablesSqlContent;
                $sqlStatements =
                    $sqlReader->getCreateTableStatementArray(implode(LF . LF, array_filter($sqlStatements)));
                $updateStatements = $schemaMigrator->getUpdateSuggestions($sqlStatements);
                $updateStatements = array_merge_recursive(...array_values($updateStatements));
                $selectedStatements = [];
                foreach (['add', 'change', 'create_table', 'change_table'] as $action) {
                    if (empty($updateStatements[$action])) {
                        continue;
                    }
                    $selectedStatements = array_merge(
                        $selectedStatements,
                        array_combine(
                            array_keys($updateStatements[$action]),
                            array_fill(0, count($updateStatements[$action]), true)
                        )
                    );
                }
                $schemaMigrator->migrate($sqlStatements, $selectedStatements);
            }
        }
    }

    public function access()
    {
        return true;
    }
}
