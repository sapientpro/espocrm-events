## Simple Events for EspoCRM
Simple Events Module for EspoCRM

## Installation
Upload zip package in the admin of the EspoCRM

## Usage
Create Entity event file in the folder: custom/Espo/Custom/Events
Events type:

        'beforeRecordSaved',
        'afterRecordSaved',
        'afterRecordCreated',
        'beforeRecordDeleted',
        'afterRecordDeleted'

## Code Example
custom/Espo/Custom/Events/Contact.php


```
namespace Espo\Custom\Events;


class Contact extends \Espo\Core\Services\Base
{

    public function afterRecordSaved(Entity $contact) 
    {    
        
        $contact->set('name', $contact->get('name') . ' [Custom Name]');
        
        $entityManager = $this->getEntityManager();        
        $entityManager->saveEntity($contact);
    }
}
```

