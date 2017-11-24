<?php

namespace Espo\Modules\Events\Hooks\Common;

use Espo\Modules\Events\Core\EventManager;

class Event extends \Espo\Core\Hooks\Base
{
    public static $order = 9;

    protected function init()
    {
        $this->addDependency('eventManager');
    }

    protected function getEventManager()
    {
        return $this->getInjection('eventManager');
    }
    
    public function beforeSave(\Espo\ORM\Entity $entity, array $options = array())
    {
        $eventManager = $this->getEventManager();

        if (!empty($options['skipEvent'])) {
            return;
        }

        $eventManager->process($entity, EventManager::BEFORE_RECORD_SAVED);
    }

    public function afterSave(\Espo\ORM\Entity $entity, array $options = array())
    {
        $eventManager = $this->getEventManager();

        if (!empty($options['skipEvent'])) {
            return;
        }

        if (!$entity->isFetched()) {
            $eventManager->process($entity, EventManager::AFTER_RECORD_CREATED);
        }

        $eventManager->process($entity, EventManager::AFTER_RECORD_SAVED);
    }
    
    public function beforeDelete(\Espo\ORM\Entity $entity, array $options = array())
    {
        $eventManager = $this->getEventManager();

        if (!empty($options['skipEvent'])) {
            return;
        }

        $eventManager->process($entity, EventManager::BEFORE_RECORD_DELETED);
    }
    
    public function afterDelete(\Espo\ORM\Entity $entity, array $options = array())
    {
        $eventManager = $this->getEventManager();

        if (!empty($options['skipEvent'])) {
            return;
        }

        $eventManager->process($entity, EventManager::AFTER_RECORD_DELETED);
    }
}