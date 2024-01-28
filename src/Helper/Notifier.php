<?php declare(strict_types=1);

namespace Julius\Framework\Helper;

final class Notifier
{
    private array   $notifications;
    private int     $size;

    public function __construct()
    {
        $this->notifications    = [];   
        $this->size             = 0; 
    }

    public function add(string $notification) : void
    {
        if(empty($notification))
            return;

        $this->notifications[] = $notification;
        $this->size++;
    }

    public function toString() : string
    {
        $text = '';

        foreach($this->notifications as $notification)
        {
            $text .= $notification . "\n";
        }

        return $text;
    }

    public function get() : array
    {
        return $this->notifications;
    }

    public function isEmpty() : bool
    {
        return $this->size === 0;
    }
}