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
        $node = new Node($object);
        $role = $node->role_system();
        if($data){
            $permissions = $data->get('permission');
            if(is_array($permissions)){

                $name = 'Account.Permission';
                $options_list = $options;
                if(!property_exists($options_list, 'limit')){
                    $options_list->limit = '*';
                    $options_list->page = 1;
                }
                $response = $node->list($name, $role, $options);
                if($response){
                    if(array_key_exists('list', $response)){
                        $list = $response['list'];
                        foreach($list as $item){
                            ddd($item);
                        }
                    }
                    $create_many = $permissions;
                    $response = $node->create_many($name, $role, $create_many, [
                        'import' => true,
                        'uuid' => false,
                        'validation' => $options['validation'] ?? true
                    ]);
                    d($response);
                }
            }
        }

        d($flags);
        d($options);
    }
}