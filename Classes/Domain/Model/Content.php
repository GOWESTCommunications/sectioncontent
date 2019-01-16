<?php
namespace GoWest\Sectioncontent\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 Armin Ruediger Vieweg <armin@v.ieweg.de>
 *                Tim Klein-Hitpass <tim.klein-hitpass@dieassetslen.de>
 *                Kai Ratzeburg <kai.ratzeburg@dieassetslen.de>
 *  (c) 2016      Michael Nuﬂbaumer <m.nussbaumer@go-west.at>
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

/**
 * Content model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Content extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * ctype
     *
     * @var string
     */
    protected $ctype;

    /**
     * colPos
     *
     * @var integer
     */
    protected $colPos;

    /**
     * sorting
     *
     * @var integer
     */
    protected $sorting;

    /**
     * colPos
     *
     * @var integer
     */
    protected $txGridelementsContainer;

    /**
     * header
     *
     * @var string
     */
    protected $header;

    /**
     * bodytext
     *
     * @var string
     */
    protected $bodytext;

    /**
     * It may contain multiple images, but TYPO3 called this field just "image"
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $image;

    /**
     * It may contain multiple assets, but TYPO3 called this field just "assets"
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $assets;

    /**
     * Categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

    /**
     * ChildContent
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GoWest\Sectioncontent\Domain\Model\Content>
     */
    protected $childContent;

    /**
     * Complete row (from database) of this content element
     *
     * @var array
     */
    protected $_contentRow = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->image = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->assets = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Setter for images
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $image)
    {
        $this->image = $image;
    }

    /**
     * Getter for images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage images
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function addImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image->attach($image);
    }

    /**
     * Remove image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function removeImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image->detach($image);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Setter for assets
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $assets
     * @return void
     */
    public function setAssets(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $assets)
    {
        $this->assets = $assets;
    }

    /**
     * Getter for assetss
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage assetss
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Add assets
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $assets
     * @return void
     */
    public function addAssets(\TYPO3\CMS\Extbase\Domain\Model\FileReference $assets)
    {
        $this->assets->attach($assets);
    }

    /**
     * Remove assets
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $assets
     * @return void
     */
    public function removeAssets(\TYPO3\CMS\Extbase\Domain\Model\FileReference $assets)
    {
        $this->assets->detach($assets);
    }
    
    /**
     * Returns assets files as array (with all attributes)
     *
     * @return array
     */
    public function getAssetsFiles()
    {
        $assetsFiles = array();
        /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $assets */
        foreach ($this->getAssets() as $assets) {
            $assetsFiles[] = $assets->getOriginalResource()->toArray();
        }
        return $assetsFiles;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Returns image files as array (with all attributes)
     *
     * @return array
     */
    public function getImageFiles()
    {
        $imageFiles = array();
        /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $image */
        foreach ($this->getImage() as $image) {
            $imageFiles[] = $image->getOriginalResource()->toArray();
        }
        return $imageFiles;
    }

    /**
     * Setter for bodytext
     *
     * @param string $bodytext bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Getter for bodytext
     *
     * @return string bodytext
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Setter for ctype
     *
     * @param string $ctype ctype
     * @return void
     */
    public function setCtype($ctype)
    {
        $this->ctype = $ctype;
    }

    /**
     * Getter for ctype
     *
     * @return string ctype
     */
    public function getCtype()
    {
        return $this->ctype;
    }

    /**
     * Setter for colPos
     *
     * @param integer $colPos colPos
     * @return void
     */
    public function setColPos($colPos)
    {
        $this->colPos = $colPos;
    }

    /**
     * Getter for colPos
     *
     * @return integer colPos
     */
    public function getColPos()
    {
        return $this->colPos;
    }

    /**
     * Setter for sorting
     *
     * @param integer $sorting sorting
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * Getter for sorting
     *
     * @return integer sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Setter for txGridelementsContainer
     *
     * @param integer $txGridelementsContainer txGridelementsContainer
     * @return void
     */
    public function setTxGridelementsContainer($txGridelementsContainer)
    {
        $this->txGridelementsContainer = $txGridelementsContainer;
    }

    /**
     * Getter for txGridelementsContainer
     *
     * @return integer txGridelementsContainer
     */
    public function getTxGridelementsContainer()
    {
        return $this->txGridelementsContainer;
    }

    
    
    
    /**
     * Setter for header
     *
     * @param string $header header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * Getter for header
     *
     * @return string header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Getter for categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Setter for categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Add category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     * @return void
     */
    public function addCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * Remove category
     *
     * @param \GoWest\Sectioncontent\Domain\Model\Content $category
     * @return void
     */
    public function removeCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $category)
    {
        $this->categories->detach($category);
    }

    /**
     * Getter for childContent
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getChildContents()
    {
        return $this->childContent;
    }

    /**
     * Setter for childContent
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $childContent
     * @return void
     */
    public function setChildContent($childContent)
    {
        $this->childContent = $childContent;
    }

    /**
     * Add childContent
     *
     * @param \GoWest\Sectioncontent\Domain\Model\Content $childContent
     * @return void
     */
    public function addChildContent(\GoWest\Sectioncontent\Domain\Model\Content $childContent)
    {
        $this->childContent->attach($childContent);
    }

    /**
     * Remove childContent
     *
     * @param \GoWest\Sectioncontent\Domain\Model\Content $childContent
     * @return void
     */
    public function removeChildContent(\GoWest\Sectioncontent\Domain\Model\Content $childContent)
    {
        $this->childContent->detach($childContent);
    }

    /**
     * Checks for attribute in _contentRow
     *
     * @param string $name Name of unknown method
     * @param array arguments Arguments of call
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr(strtolower($name), 0, 3) == 'get' && strlen($name) > 3) {
            $attributeName = lcfirst(substr($name, 3));

            if (empty($this->_contentRow)) {
                /** @var \TYPO3\CMS\Frontend\Page\PageRepository $pageSelect */
                $pageSelect = $GLOBALS['TSFE']->sys_page;
                $contentRow = $pageSelect->getRawRecord('tt_content', $this->getUid());
        
                foreach ($contentRow as $key => $value) {
                    $this->_contentRow[GeneralUtility::underscoredToLowerCamelCase($key)] = $value;
                }
            }
            if (isset($this->_contentRow[$attributeName])) {
                return $this->_contentRow[$attributeName];
            }
        }
    }

    /**
     * Get raw content row
     *
     * @return array
     */
    public function getContentRow()
    {
        return $this->_contentRow;
    }
}
