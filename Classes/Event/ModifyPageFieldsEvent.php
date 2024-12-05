<?php 

namespace GOWEST\Sectioncontent\Event;

final class ModifyPageFieldsEvent
{
    private array $pageFields;

    public function __construct(array $pageFields)
    {
        $this->pageFields = $pageFields;
    }

    public function getPageFields(): array
    {
        return $this->pageFields;
    }

    public function setPageFields(array $pageFields): void
    {
        $this->pageFields = $pageFields;
    }
}
