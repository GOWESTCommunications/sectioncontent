<?php 

namespace GOWEST\Sectioncontent\Event;

final class GetAdditionalDataEvent
{
    private array $additionalData = [];
    private array $pageUids;
    private int $languageId;

    public function __construct(array $pageUids, $languageId)
    {
        $this->pageUids = $pageUids;
        $this->languageId = $languageId;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function setAdditionalData(array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function getPageUids(): array
    {
       return $this->pageUids;
    }

    public function getLanguageId(): int
    {
       return $this->languageId;
    }
}
