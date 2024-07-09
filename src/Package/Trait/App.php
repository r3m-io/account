<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\App as Application;

trait App
{

    protected ?Application $object;

    public function object($object=null): ?Application
    {
        if($object!==null){
            $this->setObject($object);
        }
        return $this->getObject();
    }

    private function setObject(Application $object): void
    {
        $this->object = $object;
    }

    private function getObject(): ?Application
    {
        return $this->object;
    }
}