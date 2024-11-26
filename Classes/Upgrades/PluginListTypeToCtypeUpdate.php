<?php

declare(strict_types=1);

namespace GOWEST\Sectioncontent\Upgrades;

// composer req linawolf/list-type-migration
use Linawolf\ListTypeMigration\Upgrades\AbstractListTypeToCTypeUpdate;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;

#[UpgradeWizard('SectioncontentPluginListTypeToCTypeUpdate')]
final class PluginListTypeToCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'sectioncontent_pi1' => 'sectioncontent_pi1',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrates sectioncontent plugins';
    }

    public function getDescription(): string
    {
        return 'Migrates sectioncontent from list_type to CType. ';
    }
}
