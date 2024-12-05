<?php 

namespace GOWEST\Sectioncontent\Event;

final class ModifyPageDataEvent
{
    private array $pageData;
    private int $languageId;
    private $cObj;

    public function __construct(array $pageData, $languageId, $cObj)
    {
        $this->pageData = $pageData;
        $this->languageId = $languageId;
        $this->cObj = $cObj;
    }

    public function getPageData(): array
    {
        return $this->pageData;
    }

    public function setPageData(array $pageData): void
    {
        $this->pageData = $pageData;
    }

    public function getLanguageId(): int
    {
       return $this->languageId;
    }

    public function getCObj()
    {
       return $this->cObj;
    }
}
