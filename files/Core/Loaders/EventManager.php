<?php

namespace  Espo\Modules\Events\Core\Loaders;

class EventManager extends \Espo\Core\Loaders\Base
{
    public function load()
    {
        return new \Espo\Modules\Events\Core\EventManager($this->getContainer());
    }
}