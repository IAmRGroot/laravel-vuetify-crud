<?php

namespace IAmRGroot\VuetifyCRUD\Fields;

class Field
{
    public string $attribute;
    public bool $viewable;
    public bool $editable;

    public function __construct(
        string $attribute,
        bool $viewable = true,
        bool $editable = true
    ) {
        $this->attribute = $attribute;
        $this->viewable  = $viewable;
        $this->editable  = $editable;
    }

    public function customField(): ?string
    {
        return null;
    }

    public function toHeader(): string
    {
        return <<<JS
{ text: {$this->attribute}, value: {$this->attribute} }
JS;
    }
}
