<?php declare(strict_types=1);

namespace Julius\Framework\Http;

class ResponseJson
{
    private $items = [];

    public function addItem(string $key, mixed $value) : void
    {
        $this->items[$key] = $value;
    }

    public function toJson() : void
    {
        echo json_encode($this->items);
    }
}