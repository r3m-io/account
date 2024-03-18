<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\Config;

use R3m\Io\Module\Core;
use R3m\Io\Module\File;
use R3m\Io\Module\Dir;

use R3m\Io\Node\Model\Node;

use Exception;

use R3m\Io\Exception\FileWriteException;
use R3m\Io\Exception\ObjectException;

trait Main {

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function account_create_default($flags, $options){
        /*
         * - create role ROLE_SYSTEM with rank 1
         * - create role ROLE_ADMIN with rank 2
         */
        $object = $this->object();
        $object->config('r3m.io.node.import.start', microtime(true));
        $url = $object->config('project.dir.data') . 'Account/Role.System.json';
        $data = $object->data_read($url);
        $node = new Node($object);
        $role = $node->role_system();
        $create_many = [];
        $patch_many = [];
        $put_many = [];
        $list = [];
        $error = [];
        $is_transaction = false;
        $is_create = false;
        $is_patch = false;
        $is_put = false;
        $is_lock = false;
        $create = 0;
        $patch = 0;
        $put = 0;
        $skip = 0;
        $double = 0;
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
                if($response) {
                    if (array_key_exists('list', $response)) {
                        foreach ($response['list'] as $item) {
                            $list[$item->name] = $item;
                        }
                    }
                }
                $unique = [];
                foreach($permissions as $permission) {
                    if (property_exists($permission, 'name')) {
                        if(in_array($permission->name, $unique)) {
                            $double++;
                            continue;
                        }
                        $unique[] = $permission->name;
                        if (array_key_exists($permission->name, $list)) {
                            //put or patch or skip
                            if (property_exists($options, 'force')) {
                                $permission->uuid = $list[$permission->name]->uuid;
                                $put_many[] = $permission;
                                $is_transaction = true;
                                $is_put = true;
                            } elseif (property_exists($options, 'patch')) {
                                $permission->uuid = $list[$permission->name]->uuid;
                                $patch_many[] = $permission;
                                $is_transaction = true;
                                $is_patch = true;
                            } else {
                                $skip++;
                            }
                        } else {
                            //create
                            $create_many[] = $permission;
                            $is_transaction = true;
                            $is_create = true;
                        }
                    }
                }
                $commit = false;
                if($is_transaction){
                    $is_lock = $node->startTransaction($name, $options);
                    if($is_create){
                        $response = $node->create_many($name, $role, $create_many, [
                            'import' => true,
                            'uuid' => false,
                            'validation' => $options->validation ?? true
                        ]);
                        if(array_key_exists('error', $response)){
                            $error = array_merge($error, $response['error']);
                        }
                        if(array_key_exists('list', $response)){
                            $create = count($response['list']);
                        }
                    }
                    if($is_put){
                        $response = $node->put_many($name, $role, $put_many, [
                            'import' => true,
                            'validation' => $options->validation ?? true
                        ]);
                        if(array_key_exists('error', $response)){
                            $error = array_merge($error, $response['error']);
                        }
                        if(array_key_exists('list', $response)){
                            $put = count($response['list']);
                        }
                    }
                    if($is_patch){
                        $response = $node->patch_many($name, $role, $patch_many, [
                            'import' => true,
                            'validation' => $options->validation ?? true
                        ]);
                        if(array_key_exists('error', $response)){
                            $error = array_merge($error, $response['error']);
                        }
                        if(array_key_exists('list', $response)){
                            $patch = count($response['list']);
                        }
                    }
                    if(!empty($error)){
                        if($is_lock){
                            $node->unlock($name);
                        }
                        return [
                            'error' => $error,
                            'transaction' => true,
                            'duration' => (microtime(true) - $object->config('r3m.io.node.import.start')) * 1000
                        ];
                    }
                    elseif($is_lock) {
                        $commit = $node->commit($name, $role);
                    }
                }
                $duration = microtime(true) - $object->config('r3m.io.node.import.start');
                $total = $put + $patch + $create;
                $item_per_second = round($total / $duration, 2);
                $object->config('delete', 'node.transaction.' . $name);
                return [
                    'double' => $double,
                    'skip' => $skip,
                    'put' => $put,
                    'patch' => $patch,
                    'create' => $create,
                    'commit' => $commit,
                    'mtime' => File::mtime($url),
                    'duration' => $duration * 1000,
                    'item_per_second' => $item_per_second,
                    'transaction' => true
                ];
            }
        }
    }
}