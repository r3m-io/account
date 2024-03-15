<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\Config;

use R3m\Io\Module\Core;
use R3m\Io\Module\File;
use R3m\Io\Module\Dir;

use Exception;
use R3m\Io\Node\Model\Node;

trait Main {

    public function account_create_default($flags, $options){
        /*
         * - create role ROLE_SYSTEM with rank 1
         * - create role ROLE_ADMIN with rank 2
         */
        $object = $this->object();
        $url = $object->config('project.dir.data') . 'Account/Role.System.json';
        $data = $object->data_read($url);
        if($data){
            $permissions = $data->get('permission');
            if(is_array($permissions)){
                $node = new Node($object);
                $name = 'Account.Permission';
                $options_list = $options;
                if(!property_exists($options_list, 'limit')){
                    $options_list->limit = '*';
                }
                $response = $node->list($name, $node->role_system(), $options);
                ddd($response);



//                $node->import($name, $node->role_system(), $options);
            }
            ddd($permissions);
        }
        ddd($data);

        d($flags);
        d($options);
    }
}