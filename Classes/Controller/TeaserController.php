<?php
namespace GoWest\Sectioncontent\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 Armin Ruediger Vieweg <armin@v.ieweg.de>
 *                Tim Klein-Hitpass <tim.klein-hitpass@diemedialen.de>
 *                Kai Ratzeburg <kai.ratzeburg@diemedialen.de>
 *  (c) 2016      Michael Nußbaumer <m.nussbaumer@go-west.at>
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
    protected $settings = array();

    /**
     * @var integer
     */
    protected $currentPageUid = null;

    /**
     * @var \GoWest\Sectioncontent\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \GoWest\Sectioncontent\Domain\Repository\ContentRepository
     * @inject
     */
    protected $contentRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var \GoWest\Sectioncontent\Utility\Settings
     * @inject
     */
    protected $settingsUtility;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject = null;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * Initialize Action will performed before each action will be executed
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->settings = $this->settingsUtility->renderConfigurationArray($this->settings);
    }

    /**
     * Displays teasers
     *
     * @return void
     */
    public function indexAction()
    {
        $this->currentPageUid = $GLOBALS['TSFE']->id;

        $this->performTemplatePathAndFilename();
        $this->setOrderingAndLimitation();
        $this->performPluginConfigurations();

        switch ($this->settings['source']) {
            default:
            case 'thisChildren':
                $rootPageUids = $this->currentPageUid;
                $pages = $this->pageRepository->findByPid($this->currentPageUid);
                break;

            case 'thisChildrenRecursively':
                $rootPageUids = $this->currentPageUid;
                $pages = $this->pageRepository->findByPidRecursively(
                    $this->currentPageUid,
                    (int)$this->settings['recursionDepthFrom'],
                    (int)$this->settings['recursionDepth']
                );
                break;

            case 'custom':
                $rootPageUids = $this->settings['customPages'];
                $pages = $this->pageRepository->findByPidList(
                    $this->settings['customPages'],
                    $this->settings['orderByPlugin']
                );
                break;

            case 'customChildren':
                $rootPageUids = $this->settings['customPages'];
                $pages = $this->pageRepository->findChildrenByPidList($this->settings['customPages']);
                break;

            case 'customChildrenRecursively':
                $rootPageUids = $this->settings['customPages'];
                $pages = $this->pageRepository->findChildrenRecursivelyByPidList(
                    $this->settings['customPages'],
                    (int)$this->settings['recursionDepthFrom'],
                    (int)$this->settings['recursionDepth']
                );
                break;
        }
        
        if($this->settings['jsFiltertype']) {
            if($this->settings['jsFiltertype'] == 'jsFilterSelected') {
                $filterCategories = $this->getAllFilterCategories('selected');
            } elseif($this->settings['jsFiltertype'] == 'jsFilterChildOfSelected') {
                $filterCategories = $this->getAllFilterCategories('child');
            }
            
            $this->view->assign('filterCategories', $filterCategories);
            
        }


        if ($this->settings['pageMode'] !== 'nested') {
            $pages = $this->performSpecialOrderings($pages);
        }

        /** @var $page \GoWest\Sectioncontent\Domain\Model\Page */
        foreach ($pages as $page) {
            if ($page->getUid() === $this->currentPageUid) {
                $page->setIsCurrentPage(true);
            }

            // Load contents if enabled in configuration
            if ($this->settings['loadContents'] == '1') {
                $page->setContents($this->contentRepository->findByPid($page->getUid()));
            }
        }

        if ($this->settings['pageMode'] === 'nested') {
            $pages = $this->convertFlatToNestedPagesArray($pages, $rootPageUids);
        }

        $this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'ModifyPages', array(&$pages, $this));
        $this->view->assign('pages', $pages);
    }
    
    
    
    private function getAllFilterCategories($mode = 'selected') {
        
        $selectedCategories = explode(',', $this->settings['filterCategories']);
        $allCategories = $this->categoryRepository->findAll();
        $return = array();
        
        foreach($allCategories as $category) {
            if($mode == 'selected' && in_array($category->getUid(), $selectedCategories)) {
                $return[$category->getUid()] = array(
                    'title'         => $category->getTitle(),
                    'uid'           => $category->getUid(),
                );
            } elseif($mode == 'child') {
                if($category->getParent()) {            
                    if(in_array($category->getParent()->getUid(), $selectedCategories)) {
                    
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
     * @param \GoWest\Sectioncontent\Domain\Model\Page $a
     * @param \GoWest\Sectioncontent\Domain\Model\Page $b
     * @return integer
     */
    protected function sortByRecursivelySorting(
        \GoWest\Sectioncontent\Domain\Model\Page $a,
        \GoWest\Sectioncontent\Domain\Model\Page $b
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
        if (!empty($this->settings['orderBy'])) {
            if ($this->settings['orderBy'] === 'customField') {
                $this->pageRepository->setOrderBy($this->settings['orderByCustomField']);
            } else {
                $this->pageRepository->setOrderBy($this->settings['orderBy']);
            }
        }

        if (!empty($this->settings['orderDirection'])) {
            $this->pageRepository->setOrderDirection($this->settings['orderDirection']);
        }

        if (!empty($this->settings['limit']) && $this->settings['orderBy'] !== 'random') {
            $this->pageRepository->setLimit(intval($this->settings['limit']));
        }

        if (!empty($this->settings['offset']) && $this->settings['orderBy'] !== 'random') {
            $this->pageRepository->setOffset(intval($this->settings['offset']));
            
            if (empty($this->settings['limit'])) {
                $this->pageRepository->setLimit(9999999);
            }
        }
        
    }

    /**
     * Sets the fluid template to file if file is selected in flexform
     * configuration and file exists
     *
     * @return boolean Returns TRUE if templateType is file and exists,
     *         otherwise returns FALSE
     */
    protected function performTemplatePathAndFilename()
    {
        $frameworkSettings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        
        ksort($frameworkSettings['view']['layoutRootPath']);
        ksort($frameworkSettings['view']['partialRootPath']);
        ksort($frameworkSettings['view']['templateRootPath']);
        
        $this->view->assign('contentObject', $this->configurationManager->getContentObject()->data);
        $this->view->assign('tsfe', array('page' => $GLOBALS['TSFE']->page));
        $templateType = $frameworkSettings['view']['templateType'];
        $templateFile = $frameworkSettings['view']['templateRootFile'];
        $layoutRootPathArr = $frameworkSettings['view']['layoutRootPath'];
        $partialRootPathArr = $frameworkSettings['view']['partialRootPath'];
        $templateRootPathArr = $frameworkSettings['view']['templateRootPath'];
        $controllerAction = ucfirst ($this->controllerContext->getRequest()->getControllerActionName());
        
        $this->view->setLayoutRootPaths($layoutRootPathArr);
        
        $partialRootPathPossible = [];
        foreach($partialRootPathArr as $partialRootPath) {
            if ($partialRootPath != null && !empty($partialRootPath) && file_exists(PATH_site . $partialRootPath)) {
                $partialRootPathPossible[] = $partialRootPath;
            }
        }
        if(count($partialRootPathPossible)) {
            $this->view->setPartialRootPaths($partialRootPathPossible);
        }
        
        
        
        if ($templateType === 'file' && !empty($templateFile) && file_exists(PATH_site . $templateFile)) {
            $this->view->setTemplatePathAndFilename($templateFile);
            return true;
        }
        
        
        foreach($templateRootPathArr as $templateRootPath) {
            
            
            $fileName =  GeneralUtility::getFileAbsFileName(str_replace('//', '/', $templateRootPath . '/' . $controllerAction . '.html'));
            
            if ($templateType === 'directory' && !empty($templateRootPath) && file_exists($fileName)) {
                $this->view->setTemplatePathAndFilename($fileName);
                return true;
            }
        
            if (!empty($templateRootPath) && file_exists($fileName)) {
                $this->view->setTemplatePathAndFilename($fileName);
                return true;
            }
        }

        $templatePathAndFilename = str_replace('//', '/', $frameworkSettings['view']['templatePathAndFilename']);
        if ($templateType === null && !empty($templatePathAndFilename)
            && file_exists(PATH_site . $templatePathAndFilename)) {
            $this->view->setTemplatePathAndFilename($templatePathAndFilename);
            return true;
        }
        return false;
    }

    /**
     * Performs configurations from plugin settings (flexform)
     *
     * @return void
     */
    protected function performPluginConfigurations()
    {
        // Set ShowNavHiddenItems to TRUE
        $this->pageRepository->setShowNavHiddenItems(($this->settings['showNavHiddenItems'] == '1'));
        $this->pageRepository->setFilteredDokType(GeneralUtility::trimExplode(
            ',',
            $this->settings['showDoktypes'],
            true
        ));

        if ($this->settings['hideCurrentPage'] == '1') {
            $this->pageRepository->setIgnoreOfUid($this->currentPageUid);
        }

        if ($this->settings['ignoreUids']) {
            $ignoringUids = GeneralUtility::trimExplode(',', $this->settings['ignoreUids'], true);
            array_map(array($this->pageRepository, 'setIgnoreOfUid'), $ignoringUids);
        }

        if ($this->settings['categoriesList'] && $this->settings['categoryMode']) {
            $categories = array();
            foreach (GeneralUtility::intExplode(',', $this->settings['categoriesList'], true) as $categoryUid) {
                $categories[] = $this->categoryRepository->findByUid($categoryUid);
            }

            switch ((int)$this->settings['categoryMode']) {
                case \GoWest\Sectioncontent\Domain\Repository\PageRepository::CATEGORY_MODE_OR:
                case \GoWest\Sectioncontent\Domain\Repository\PageRepository::CATEGORY_MODE_OR_NOT:
                    $isAnd = false;
                    break;
                default:
                    $isAnd = true;
            }
            switch ((int)$this->settings['categoryMode']) {
                case \GoWest\Sectioncontent\Domain\Repository\PageRepository::CATEGORY_MODE_AND_NOT:
                case \GoWest\Sectioncontent\Domain\Repository\PageRepository::CATEGORY_MODE_OR_NOT:
                    $isNot = true;
                    break;
                default:
                    $isNot = false;
            }
            $this->pageRepository->addCategoryConstraint($categories, $isAnd, $isNot);
        }

        if ($this->settings['source'] === 'custom') {
            $this->settings['pageMode'] = 'flat';
        }

        if ($this->settings['pageMode'] === 'nested') {
            $this->settings['recursionDepthFrom'] = 0;
            $this->settings['orderBy'] = 'uid';
            $this->settings['limit'] = 0;
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
        // Make random if selected on queryResult, cause Extbase doesn't support it
        if ($this->settings['orderBy'] === 'random') {
            shuffle($pages);
            if (!empty($this->settings['limit'])) {
                $pages = array_slice($pages, 0, $this->settings['limit']);
            }
        }

        if ($this->settings['orderBy'] === 'sorting' && strpos($this->settings['source'], 'Recursively') !== false) {
            usort($pages, array($this, 'sortByRecursivelySorting'));
            if (strtolower($this->settings['orderDirection']) === strtolower(QueryInterface::ORDER_DESCENDING)) {
                $pages = array_reverse($pages);
            }
            if (!empty($this->settings['limit'])) {
                $pages = array_slice($pages, 0, $this->settings['limit']);
                return $pages;
            }
            return $pages;
        }
        return $pages;
    }

    /**
     * Converts given pages array (flat) to nested one
     *
     * @param array <Pages> $pages
     * @param string $rootPageUids Comma separated list of page uids
     * @return array<Pages>
     */
    protected function convertFlatToNestedPagesArray($pages, $rootPageUids)
    {
        $rootPageUidArray = GeneralUtility::intExplode(',', $rootPageUids);
        $rootPages = array();
        foreach ($rootPageUidArray as $rootPageUid) {
            $page = $this->pageRepository->findByUid($rootPageUid);
            $this->fillChildPagesRecursivley($page, $pages);
            $rootPages[] = $page;
        }
        return $rootPages;
    }

    /**
     * Fills given parentPage's childPages attribute recursively with pages
     *
     * @param \GoWest\Sectioncontent\Domain\Model\Page $parentPage
     * @param array $pages
     * @return \GoWest\Sectioncontent\Domain\Model\Page
     */
    protected function fillChildPagesRecursivley($parentPage, array $pages)
    {
        $childPages = array();
        /** @var $page \GoWest\Sectioncontent\Domain\Model\Page */
        foreach ($pages as $page) {
            if ($page->getPid() === $parentPage->getUid()) {
                $this->fillChildPagesRecursivley($page, $pages);
                $childPages[$page->getSorting()] = $page;
            }
        }
        ksort($childPages);
        $parentPage->setChildPages(array_values($childPages));
        return $parentPage;
    }
}
