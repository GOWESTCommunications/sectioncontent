<?php

namespace GOWEST\Sectioncontent\Backend;

class ItemsProcFunc
{
    /**
     * Modifies the select box of orderBy-options.
     *
     * @param array &$config configuration array
     */
    public function sectioncontentLayouts(array &$config)
    {
        $config['items'] =
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sectioncontent_pi1']['layouts']
            ? $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sectioncontent_pi1']['layouts']
            : [
                ['Default', 'default'],
            ];
    }
}
