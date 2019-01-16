<?php
namespace GoWest\Sectioncontent\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 Armin Ruediger Vieweg <armin@v.ieweg.de>
 *                Tim Klein-Hitpass <tim.klein-hitpass@diemedialen.de>
 *                Kai Ratzeburg <kai.ratzeburg@diemedialen.de>
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

/**
 * Repository for Content model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ContentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Initializes the repository.
     *
     * @return void
     * @see \TYPO3\CMS\Extbase\Persistence\Repository::initializeObject()
     */
    public function initializeObject()
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
        $querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface');
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns all objects of this repository which matches the given pid. This
     * overwritten method exists, to perform sorting
     *
     * @param integer $pid Pid to search for
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult All found objects, will be
     *         empty if there are no objects
     */
    public function findByPid($pid)
    {
        $query = $this->createQuery();
        
        
        
        $query->matching(
            $query->logicalAnd(
                $query->equals('pid', $pid),
                $query->logicalOr(
                            $query->equals('tx_gridelements_container', '0'),
                            $query->equals('tx_gridelements_container', '-1')
                )
            )
        );
        
        
        $query->setOrderings(array(
            'colPos' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
        ));
        
        $contentElements = $query->execute();
        $contentElements = $this->getChildContent($contentElements);
        
        
        
        return $contentElements;
    }
    
    protected function getChildContent($contentElements) {
        $orderedArray = array();
        foreach($contentElements as $ce) {
            if($ce->getCtype() == 'gridelements_pi1') {
                $query = $this->createQuery();
                $query->matching(
                    $query->logicalAnd(
                        $query->equals('pid', $ce->getPid()),
                        $query->equals('tx_gridelements_container', $ce->getUid())
                    )
                );
                $query->setOrderings(array(
                    'tx_gridelements_columns' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
                    'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
                ));
                
                $childContent = $query->execute();
                $childContent = $this->getChildContent($childContent);
                $ce->setChildContent($childContent);
            }
        }
        
        
        
        return $contentElements;
    }

    /**
     * Returns all objects of this repository which are located inside the
     * given pages
     *
     * @param array <\GoWest\Sectioncontent\Domain\Model\Page> $pages Pages to get content elements
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult All found objects, will be
     *         empty if there are no objects
     */
    public function findByPages($pages)
    {
        $query = $this->createQuery();
        $constraint = array();

        foreach ($pages as $page) {
            $constraint[] = $query->equals('pid', $page->getUid());
        }

        $query->matching($query->logicalOr($constraint));

        return $query->execute();
    }
}
