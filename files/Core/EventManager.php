<?php

namespace Espo\Modules\Events\Core;

use Espo\Core\Utils\Json;
use Espo\Core\Utils\Util;

class EventManager 
{
    
    protected $cacheFile = 'data/cache/events/events.php';
    
    const BEFORE_RECORD_SAVED = 'beforeRecordSaved';
    
    const AFTER_RECORD_SAVED = 'afterRecordSaved';
    const AFTER_RECORD_CREATED = 'afterRecordCreated';
    
    const BEFORE_RECORD_DELETED = 'beforeRecordDeleted';
    const AFTER_RECORD_DELETED = 'afterRecordDeleted';
    
    private $path = 'custom/Espo/Custom/Events';
    
    protected $allowedMethod = array(
        'beforeRecordSaved',
        'afterRecordSaved',
        'afterRecordCreated',
        'beforeRecordDeleted',
        'afterRecordDeleted'
    );

    public function __construct(\Espo\Core\Container $container)
    {
        $this->container = $container;
    }
    
    protected function getContainer()
    {
        return $this->container;
    }
    
    protected function getConfig()
    {
        return $this->getContainer()->get('config');
    }
    
    protected function getFileManager()
    {
        return $this->getContainer()->get('fileManager');
    }
    
    protected function getEntityManager()
    {
        return $this->getContainer()->get('entityManager');
    }
    
    protected function getUser()
    {
        return $this->getContainer()->get('user');
    }
    
    public function process(\Espo\Orm\Entity $entity, $eventType)
    {
        $entityName = $entity->getEntityType();
        
        $GLOBALS['log']->debug('EventManager: Start event ['.$eventType.'] for Entity ['.$entityName.', '.$entity->id.'].');        
        
        if($eventClass = $this->getEvent($entityName, $eventType)) {
            $event = new $eventClass();
            $event->inject('entityManager', $this->getEntityManager());
            $event->inject('user', $this->getUser());
            $event->$eventType($entity);    
        }
        
        $GLOBALS['log']->debug('EventManager: Stop  event ['.$eventType.'] for Entity ['.$entityName.', '.$entity->id.'].');
    }
    
    
    public function getEvent($entityName, $eventType) {
        
        if(file_exists($this->cacheFile) && $this->getConfig()->get('useCache')) {
            $fileList = $this->getFileManager()->getPhpContents($this->cacheFile);
        }else {        
            $fileList = $this->getFileManager()->getFileList($this->path, 1, '\.php$', true);
            if ($this->cacheFile && $this->getConfig()->get('useCache')) {
                $result = $this->getFileManager()->putPhpContents($this->cacheFile, $fileList);
                if ($result == false) {
                    throw new \Espo\Core\Exceptions\Error();
                }
            }
        }
        
        $normalizedName = Util::normilizeClassName($entityName);
        
        foreach($fileList as $file) {
            if($file == $normalizedName . '.php') {
                $scopeDirPath = Util::concatPath($this->path, $file);
                $className = Util::getClassName($scopeDirPath);
                $classMethods = get_class_methods($className);
                
                if(in_array($eventType, $classMethods)) {
                    return $className;
                }
            }
        }
        
        return false;
        
    }
    
}