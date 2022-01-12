<?php

namespace GoWest\Sectioncontent\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020      Michael Nußbaumer <m.nussbaumer@go-west.at>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use FriendsOfTYPO3\Headless\Utility\FileUtility;

/**
 * Controller for the Teaser object
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TeaserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var integer
     */
    protected $currentPageUid = null;

    /**
     * @var \GoWest\Sectioncontent\Utility\Settings
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $settingsUtility;

    protected $defaultViewObjectName = JsonView::class;

    /** Category Mode: Or */
    const CATEGORY_MODE_OR = 1;
    /** Category Mode: And */
    const CATEGORY_MODE_AND = 2;
    /** Category Mode: Or Not */
    const CATEGORY_MODE_OR_NOT = 3;
    /** Category Mode: And Not */
    const CATEGORY_MODE_AND_NOT = 4;
    /** Category Mode: Current Page Categories And */
    const CATEGORY_MODE_CURRENT_AND = 5;
    /** Category Mode: Current Page Categories Or */
    const CATEGORY_MODE_CURRENT_OR = 6;

    /**
     * Initialize Action will performed before each action will be executed
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->siteFinder = $this->objectManager->get(SiteFinder::class);
        $this->imageService = $this->objectManager->get(ImageService::class);
        $this->settings = $this->settingsUtility->renderConfigurationArray($this->settings);
        $this->languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $this->sys_language_uid = $this->languageAspect->getId();
        $this->dbConnections = [
            'pages'                     => GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages'),
            'sys_file_reference'        => GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference'),
            'sys_category_record_mm'    => GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_category_record_mm'),
        ];
    }

    /**
     * Displays teasers
     *
     * @return void
     */
    public function indexAction()
    {

        $this->currentPageUid = $GLOBALS['TSFE']->id;
        $this->includePages = [];
        $this->excludePages = [];
        $this->pages = [];
        $this->onlyIncluded = false;
        $this->selectFields = [
            'p.uid',
            'p.pid',
            'IF(p.starttime = 0, p.crdate, p.starttime) as publish_date',
            'IF(p.tx_sectioncontent_abstract_title != "", p.tx_sectioncontent_abstract_title, p.title) as teaser_title',
            'IF(p.tx_sectioncontent_abstract_subtitle != "", p.tx_sectioncontent_abstract_subtitle, p.subtitle) as teaser_subtitle',
            'IF(p.tx_sectioncontent_abstract_description != "", p.tx_sectioncontent_abstract_description, p.abstract) as teaser_description',
            'p.sys_language_uid',
            'p.author',
            'p.author_email',
            'p.tx_sectioncontent_abstract_attr_1',
            'p.tx_sectioncontent_abstract_attr_2',
            'p.tx_sectioncontent_abstract_attr_3',
            'p.tx_sectioncontent_abstract_attr_4',
            'p.tx_sectioncontent_abstract_attr_5',
            'p.tx_sectioncontent_abstract_attr_6',
            'p.tx_sectioncontent_abstract_attr_7',
            'p.tx_sectioncontent_abstract_attr_8',
            'p.media',
            'p.tx_sectioncontent_abstract_image',
            'p.tx_sectioncontent_abstract_image_2',
            'p.tx_sectioncontent_abstract_image_3',
            'p.tx_sectioncontent_abstract_image_4',
            'p.tx_sectioncontent_abstract_reference_url',
        ];

        $this->baseQuery = "
            SELECT 
                ###SELECT_FIELDS###
            FROM
                pages p 
            WHERE
                (
                    (
                        FIND_IN_SET(pid, '###SELECTED_UIDS###')
                    )
                    AND sys_language_uid = ###SYS_LANGUAGE_UID###
                )
                AND ###ADD_WHERE###
                AND ###SPECIAL_ADD_WHERE###
            
            ORDER BY ###ORDER_BY###
            LIMIT ###LIMIT###
        ";

        $this->baseQueryCustomUids = "
            SELECT 
                ###SELECT_FIELDS###
            FROM
                pages p
            WHERE
                (
                    (
                        FIND_IN_SET(uid, '###SELECTED_UIDS###')
                        OR FIND_IN_SET(l10n_parent, '###SELECTED_UIDS###')
                    )
                    AND sys_language_uid = ###SYS_LANGUAGE_UID###
                )
                AND ###ADD_WHERE###
                AND ###SPECIAL_ADD_WHERE###
                    
            ORDER BY ###ORDER_BY###
            LIMIT ###LIMIT###
        ";

        $this->baseQueryRecursive = "
            WITH RECURSIVE collected_pages as (
                SELECT
                    p.*,
                    0 as level
                FROM
                    pages p
                WHERE
                (
                    (
                        FIND_IN_SET(p.uid, '###SELECTED_UIDS###')
                        OR FIND_IN_SET(p.l10n_parent, '###SELECTED_UIDS###')
                    )
                    AND p.sys_language_uid = ###SYS_LANGUAGE_UID###
                )
                AND ###ADD_WHERE###
                UNION ALL
                SELECT
                    p.*,
                    cp.level+1 as level
                FROM
                    pages p
                INNER JOIN 
                    collected_pages cp on p.pid = cp.uid
                WHERE p.sys_language_uid = ###SYS_LANGUAGE_UID###
                AND ###ADD_WHERE###
            )
            SELECT 
                ###SELECT_FIELDS###,
                level
            FROM collected_pages p
            WHERE ###SPECIAL_ADD_WHERE###
            ORDER BY ###ORDER_BY###
            LIMIT ###LIMIT###;
        ";

        switch ($this->settings['source']) {
            default:
            case 'thisChildren':
                $this->rootPageUids = $this->currentPageUid;
                $this->baseQuery = $this->baseQuery;
                break;

            case 'thisChildrenRecursively':
                $this->rootPageUids = $this->currentPageUid;
                $this->baseQuery = $this->baseQueryRecursive;
                break;

            case 'custom':
                $this->rootPageUids = $this->settings['customPages'];
                $this->baseQuery = $this->baseQueryCustomUids;
                break;

            case 'customChildren':
                $this->rootPageUids = $this->settings['customPages'];
                $this->baseQuery = $this->baseQuery;
                break;

            case 'customChildrenRecursively':
                $this->rootPageUids = $this->settings['customPages'];
                $this->baseQuery = $this->baseQueryRecursive;
                break;
        }
        $this->baseQueryRaw = $this->baseQuery;
        $this->rootPageUidsArr = GeneralUtility::intExplode(',', $this->rootPageUids);


        $this->setOrderingAndLimitation();
        $this->performPluginConfigurations();


        $this->baseQuery = str_replace(
            [
                '###SELECT_FIELDS###',
                '###SELECTED_UIDS###',
                '###SYS_LANGUAGE_UID###',
                '###ADD_WHERE###',
                '###SPECIAL_ADD_WHERE###',
                '###ORDER_BY###',
                '###LIMIT###',
            ],
            [
                implode(',', $this->selectFields),
                $this->rootPageUids,
                $this->sys_language_uid,
                $this->addWhere,
                $this->specialAddWhere,
                $this->orderBy,
                $this->limit,
            ],
            $this->baseQueryRaw
        );

        $this->allPages = [
            'uids' => [],
            'pageInfo' => [],
        ];

        $this->pages = [
            'uids' => [],
            'pageInfo' => [],
        ];

        $statement = $this->dbConnections['pages']->prepare($this->baseQuery);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $row['categories'] = [];
            $this->allPages['uids'][$row['uid']] = $row['uid'];
            $this->allPages['pageInfo'][$row['uid']] = $row;
        }

        $this->handleCategories();
        $this->addPageCategories();

        if ($this->onlyIncluded == true) {
            foreach ($this->includePages as $pageUid) {
                $this->pages['uids'][$pageUid] = $pageUid;
                $this->pages['pageInfo'][$pageUid] = $this->allPages['pageInfo'][$pageUid];
            }
        } else {
            foreach ($this->excludePages as $pageUid) {
                unset($this->allPages['uids'][$pageUid]);
                unset($this->allPages['pageInfo'][$pageUid]);
            }

            $this->pages = $this->allPages;
        }

        $this->getFileReferences();

        foreach ($this->pages['pageInfo'] as &$pageInfo) {
            $pageInfo = $this->getPageData($pageInfo);
        }

        $this->pages = $this->performSpecialOrderings($this->pages);
        $this->pages['layout'] = $this->settings['layout'];
        
        // overview link
        $instructions = [
            'parameter' => $this->settings['link'],
        ];
        $link = $this->contentObject->typoLink_URL($instructions);

        $this->pages['link'] = $link;
        $this->pages['linktext'] = $this->settings['linktext'];

        
        return json_encode($this->pages);
    }

    protected function getPageData($pageInfo)
    {
        $mediaFields = [
            'media',
            'tx_sectioncontent_abstract_image',
            'tx_sectioncontent_abstract_image_2',
            'tx_sectioncontent_abstract_image_3',
            'tx_sectioncontent_abstract_image_4',
        ];
        $newPageInfo = $pageInfo;

        foreach ($mediaFields as $mediaField) {
            $newPageInfo[$mediaField] = [];
            if (is_array($this->fileReferences[$newPageInfo['uid']][$mediaField])) {

                foreach ($this->fileReferences[$newPageInfo['uid']][$mediaField] as $fileReference) {
                    $image = $this->imageService->getImage($fileReference['uid'], null, true);
                    $image = $this->getFileUtility()->processFile($image);
                    $image['properties']['crop'] = json_decode($image['properties']['crop'], 1);
                    $newPageInfo[$mediaField][] = $image;
                }
            }
        }

        $site = $this->siteFinder->getSiteByPageId($newPageInfo['uid']);
        $newPageInfo['link'] = $site->getRouter()->generateUri($newPageInfo['uid'], ['L' => 1])->getPath();
        $instructions = [
            'parameter' =>  't3://page?uid='.$newPageInfo['uid'],
        ];
        $newPageInfo['link'] = $this->contentObject->typoLink_URL($instructions);

        return $newPageInfo;
    }

    protected function addPageCategories() {

        $catJoinCol = ($this->sys_language_uid > 0) ? 'l10n_parent' : 'uid';

        $categoryQuery = "
            SELECT
                cmm.uid_local AS category_uid, 
                cmm.uid_foreign AS page_uid,
                c.title,
                c.sys_language_uid
            FROM
                sys_category_record_mm cmm JOIN sys_category c ON c." . $catJoinCol . " = cmm.uid_local
            WHERE
                cmm.tablenames = 'pages' 
                AND cmm.fieldname = 'categories'
                AND FIND_IN_SET(cmm.uid_foreign, '" . implode(',', $this->allPages['uids']) . "')
                AND c.hidden = 0
                AND c.deleted = 0
                AND c.starttime <= UNIX_TIMESTAMP()
                AND IF(c.endtime = 0, true, (c.endtime >= UNIX_TIMESTAMP()))
        ";

        $statement = $this->dbConnections['sys_category_record_mm']->prepare($categoryQuery);
        $statement->execute();
        while ($row = $statement->fetch()) {
            if(isset($this->allPages['pageInfo'][$row['page_uid']])) {
                $this->allPages['pageInfo'][$row['page_uid']]['categories'][] = [
                    'cat_id' => $row['category_uid'],
                    'title' => $row['title'],
                ];
            }
        }
    }

    protected function getAllFilterCategories($mode = 'selected')
    {

        $selectedCategories = GeneralUtility::trimExplode(',', $this->settings['filterCategories'], true);
        $allCategories = $this->categoryRepository->findAll();
        $return = array();

        foreach ($allCategories as $category) {
            if ($mode == 'selected' && in_array($category->getUid(), $selectedCategories)) {
                $return[$category->getUid()] = array(
                    'title'         => $category->getTitle(),
                    'uid'           => $category->getUid(),
                );
            } elseif ($mode == 'child') {
                if ($category->getParent()) {
                    if (in_array($category->getParent()->getUid(), $selectedCategories)) {

                        $return[$category->getParent()->getUid()]['title'] = $category->getParent()->getTitle();
                        $return[$category->getParent()->getUid()]['uid'] = $category->getParent()->getUid();

                        $return[$category->getParent()->getUid()][$category->getUid()] = array(
                            'title'         => $category->getTitle(),
                            'uid'           => $category->getUid(),
                        );
                    }
                }
            }
        }
        
        return $return;
    }


    /**
     * Function to sort given pages by recursiveRootLineOrdering string
     *
     * @param array $a
     * @param array $b
     * @return integer
     */
    protected function sortByRecursivelySorting(
        $a,
        $b
    ) {
        if ($a->getRecursiveRootLineOrdering() == $b->getRecursiveRootLineOrdering()) {
            return 0;
        }
        return ($a->getRecursiveRootLineOrdering() < $b->getRecursiveRootLineOrdering()) ? -1 : 1;
    }

    /**
     * Sets ordering and limitation settings from $this->settings
     *
     * @return void
     */
    protected function setOrderingAndLimitation()
    {
        $this->orderBy = 'uid ASC';
        $this->orderDirection = 'ASC';
        $this->limit = 99999999;
        $this->offset = 0;
        if (!empty($this->settings['orderDirection'])) {
            $this->orderDirection = $this->settings['orderDirection'];
        }

        if (!empty($this->settings['orderBy'])) {
            if ($this->settings['orderBy'] === 'customField') {
                $this->orderBy = $this->settings['orderByCustomField'] . ' ' . $this->orderDirection;
            } else if($this->settings['orderBy'] === 'random') {
                $this->orderBy = 'RAND()';
            } else {
                $this->orderBy = str_replace("title", "teaser_title", $this->settings['orderBy']) . ' ' . $this->orderDirection;
            }
        }
        if (!empty($this->settings['offset'])) {
            $this->offset = $this->settings['offset'];
        }
        // if($this->settings['orderBy'] !== 'random') {
        //     if (!empty($this->settings['limit'])) {
        //         $this->limit = $this->settings['limit'];
        //     }

        //     if (!empty($this->settings['offset'])) {
        //         $this->limit = $this->settings[offset''] . ',' . $this->settings['limit'];
        //     }
        // }
        if(!empty($this->settings['orderByPlugin'])) {
            $this->orderBy = "FIELD(uid, ".$this->settings['customPages'].") ASC";
        }
    }

    /**
     * Performs configurations from plugin settings (flexform)
     *
     * @return void
     */
    protected function performPluginConfigurations()
    {
        $this->addWhere = '';
        $this->specialAddWhere = '';
        $addWhereArr = [];
        $specialAddWhereArr = [];

        # Base visible states
        $addWhereArr[] = 'p.hidden = 0';
        $addWhereArr[] = 'p.deleted = 0';
        $addWhereArr[] = 'p.starttime <= UNIX_TIMESTAMP()';
        $addWhereArr[] = 'IF(p.endtime = 0, true, (p.endtime >= UNIX_TIMESTAMP()))';

        if ($this->settings['showNavHiddenItems'] != '1') {
            $addWhereArr[] = 'p.nav_hide = 0';
        }

        if (!empty($this->settings['showDoktypes'])) {
            $addWhereArr[] = "FIND_IN_SET(p.doktype, '" . $this->settings['showDoktypes'] . "')";
        }

        if ($this->settings['hideCurrentPage'] == '1') {
            $specialAddWhereArr[] = " NOT FIND_IN_SET(p.uid, '" . $this->currentPageUid . "')";
        }
        
        if ($this->settings['ignoreUids']) {
            $specialAddWhereArr[] = " NOT FIND_IN_SET(p.uid, '" . $this->settings['ignoreUids'] . "')";
        }


        if($this->settings['source'] == 'thisChildrenRecursively' || $this->settings['source'] == 'customChildrenRecursively') {
            $specialAddWhereArr[] = " NOT FIND_IN_SET(p.uid, '" . $this->rootPageUids . "')";

            if($this->settings['recursionDepthFrom'] > 0) {
                $specialAddWhereArr[] = " level >= " . (int)$this->settings['recursionDepthFrom'] . " ";
            }
            if($this->settings['recursionDepth'] < 255) {
                $specialAddWhereArr[] = " level <= " . (int)$this->settings['recursionDepth'] . " ";
            }
        }


        $this->addWhere = '(' . implode(' AND ', $addWhereArr) . ')';

        if(count($specialAddWhereArr) > 0) {
            $this->specialAddWhere = '(' . implode(' AND ', $specialAddWhereArr) . ')';
        } else {
            $this->specialAddWhere = '1';
        }
    }

    protected function getFileReferences()
    {
        $this->fileReferences = [];
        $this->fileReferenceQuery = "
            SELECT
                *
            FROM 
                sys_file_reference
            WHERE
                tablenames = 'pages'
                AND hidden = 0
                AND deleted = 0
                AND FIND_IN_SET(uid_foreign, '###PAGE_UIDS###')
        ";


        $this->fileReferenceQuery = str_replace(
            [
                '###PAGE_UIDS###',
            ],
            [
                implode(',', $this->pages['uids']),
            ],
            $this->fileReferenceQuery
        );


        $statement = $this->dbConnections['sys_file_reference']->prepare($this->fileReferenceQuery);
        $statement->execute();
        while ($row = $statement->fetch()) {
            // Do something with that single row
            $this->fileReferences[$row['uid_foreign']][$row['fieldname']][$row['uid']] = $row;
        }
    }

    protected function handleCategories()
    {
        $this->categories = [];
        if (!empty($this->settings['categoriesList']) && !empty($this->settings['categoryMode'])) {
            $filterCategories = GeneralUtility::trimExplode(',', $this->settings['categoriesList'], true);
            
            if (

                (int)$this->settings['categoryMode'] == $this::CATEGORY_MODE_CURRENT_AND
                || (int)$this->settings['categoryMode'] == $this::CATEGORY_MODE_CURRENT_OR
            ) {
                $filterCategories = [];
                $this->categoryQuery = "
                    SELECT
                        uid_local,
                        uid_foreign
                    FROM 
                        sys_category_record_mm
                    WHERE
                        tablenames = 'pages' 
                        AND fieldname = 'categories'
                        AND FIND_IN_SET(uid_foreign, " . $this->currentPageUid . ")
                ";



                $statement = $this->dbConnections['sys_category_record_mm']->prepare($this->categoryQuery);
                $statement->execute();
                while ($row = $statement->fetch()) {
                    // Do something with that single row
                    $filterCategories[$row['uid_local']] = $row['uid_local'];
                }
            }

            $this->categoryQuery = "
                SELECT
                    uid_local,
                    uid_foreign
                FROM 
                    sys_category_record_mm
                WHERE
                    tablenames = 'pages' 
                    AND fieldname = 'categories'
                    AND FIND_IN_SET(uid_local, '###CATEGORY_LIST###')
                    AND FIND_IN_SET(uid_foreign, '###PAGE_UIDS###')
            ";


            $this->includePages = [];
            $this->excludePages = [];

            $this->categoryQuery = str_replace(
                [
                    '###CATEGORY_LIST###',
                    '###PAGE_UIDS###',
                ],
                [
                    $this->settings['categoriesList'],
                    implode(',', $this->allPages['uids']),
                ],
                $this->categoryQuery
            );

            $statement = $this->dbConnections['sys_category_record_mm']->prepare($this->categoryQuery);
            $statement->execute();

            // add empty category in case no page match that only included are used
            $this->categories[0][0] = 0;
            while ($row = $statement->fetch()) {
                // Do something with that single row
                $this->categories[$row['uid_foreign']][$row['uid_local']] = $row['uid_local'];
            }
            
            foreach ($this->categories as $page_id => $catInfo) {
                switch ((int)$this->settings['categoryMode']) {
                    case $this::CATEGORY_MODE_OR:
                    case $this::CATEGORY_MODE_CURRENT_OR:
                        $this->onlyIncluded = true;
                        $result = array_intersect($filterCategories, $catInfo);
                        if (count($result) > 0) {
                            $this->includePages[$page_id] = $page_id;
                        } else {
                            $this->excludePages[$page_id] = $page_id;
                        }
                        break;
                    case $this::CATEGORY_MODE_AND:
                    case $this::CATEGORY_MODE_CURRENT_AND:
                        $this->onlyIncluded = true;
                        $result = array_intersect($filterCategories, $catInfo);
                        if (count($result) == count($filterCategories)) {
                            $this->includePages[$page_id] = $page_id;
                        } else {
                            $this->excludePages[$page_id] = $page_id;
                        }
                        break;
                    case $this::CATEGORY_MODE_OR_NOT:
                        $result = array_intersect($filterCategories, $catInfo);
                        if (count($result) > 0) {
                            $this->excludePages[$page_id] = $page_id;
                        } else {
                            $this->includePages[$page_id] = $page_id;
                        }
                        break;
                    case $this::CATEGORY_MODE_AND_NOT:
                        $result = array_intersect($filterCategories, $catInfo);
                        if (count($result) == count($filterCategories)) {
                            $this->excludePages[$page_id] = $page_id;
                        } else {
                            $this->includePages[$page_id] = $page_id;
                        }
                        break;
                    default:
                }
            }
        }
    }


    /**
     * Performs special orderings like "random" or "sorting"
     *
     * @param array <Pages> $pages
     * @return array
     */
    protected function performSpecialOrderings(array $pages)
    {
        $newUids = [];
        $sorted = [];
        foreach($pages['pageInfo'] as $page) {
            $sorted[] = $page;
        }
        $limit = !empty($this->settings['limit']) ? $this->settings['limit'] : count($sorted);
        $sorted = array_slice($sorted, $this->offset, $limit);
        foreach($sorted as $page) {
            $newUids[$page['uid']] = $page['uid'];
        }

        return [
            'uids' => $newUids,
            'pageInfo' => $sorted,
        ];
    }

    /**
     * @return FileUtility
     */
    protected function getFileUtility(): FileUtility
    {
        return GeneralUtility::makeInstance(FileUtility::class);
    }
}
