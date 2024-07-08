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
    public function account_create_default($flags, $options): bool | array
    {
        /*
         * - create role ROLE_SYSTEM with rank 1
         * - create role ROLE_ADMIN with rank 2
         */
        Core::interactive();
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
                            $logger = false;
                            if($object->config('framework.environment') === Config::MODE_DEVELOPMENT){
                                $logger = $object->config('project.log.debug');
                            }
                            if($logger){
                                $object->logger($logger)->info('double permission found: ' . $url, ['permission' => $permission->name]);
                            }
                            $double++;
                            continue;
                        }
                        $unique[] = $permission->name;
                        if (array_key_exists($permission->name, $list)) {
                            //put or patch or skip
                            if (property_exists($options, 'force')) {
                                $permission->uuid = $list[$permission->name]->uuid;
                                $put_many[] = $permission;
                                $put++;
                                $is_transaction = true;
                                $is_put = true;
                            } elseif (property_exists($options, 'patch')) {
                                $permission->uuid = $list[$permission->name]->uuid;
                                $patch_many[] = $permission;
                                $patch++;
                                $is_transaction = true;
                                $is_patch = true;
                            } else {
                                $skip++;
                            }
                        } else {
                            //create
                            $create_many[] = $permission;
                            $create++;
                            $is_transaction = true;
                            $is_create = true;
                        }
                    }
                }
                $commit = false;
                if($is_transaction){
                    $object->config('r3m.io.node.import.list.count', $create + $put + $patch);
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
                            'validation' => $options->validation ?? true,
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
                            'validation' => $options->validation ?? true,
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

                //create role ROLE_SYSTEM
                //create role ROLE_ADMIN

                $roles = [
                    [
                        'name' => 'ROLE_SYSTEM',
                        'rank' => 1,
                        'permission' => '*'
                    ],
                    [
                        'name' => 'ROLE_ADMIN',
                        'rank' => 2,
                        'permission' => '*'
                    ]
                ];
                $name = 'Account.Role';
                $node_list = [];
                foreach($roles as $roles_role){
                    $record = $node->record($name, $role, [
                        'filter' => [
                            'name' => $roles_role['name']
                        ]
                    ]);
                    if($record){
                        if(array_key_exists('node', $record)){
                            if(property_exists($options, 'force')){
                                $output = [];
                                $command = Core::binary($object) .
                                    ' r3m_io/node' .
                                    ' put' .
                                    ' -class=Account.Role' .
                                    ' -uuid=' . $record['node']->uuid .
                                    ' -name=' . $roles_role['name'] .
                                    ' -rank=' . $roles_role['rank'] .
                                    ' -permission=' . $roles_role['permission']
                                ;
//                                echo $command . PHP_EOL;
                                exec($command, $output, $code);
                                if($code === 0){
                                    $item = Core::object(implode(PHP_EOL, $output), Core::OBJECT_OBJECT);
                                    if($item){
                                        $node_list[] = $item;
                                    }
                                }
                            }
                            elseif(property_exists($options, 'patch')){
                                $output = [];
                                $command = Core::binary($object) .
                                    ' r3m_io/node' .
                                    ' patch' .
                                    ' -class=Account.Role' .
                                    ' -uuid=' . $record['node']->uuid .
                                    ' -name=' . $roles_role['name'] .
                                    ' -rank=' . $roles_role['rank'] .
                                    ' -permission=' . $roles_role['permission']
                                ;
//                                echo $command . PHP_EOL;
                                exec($command, $output, $code);
                                if($code === 0){
                                    $item = Core::object(implode(PHP_EOL, $output), Core::OBJECT_OBJECT);
                                    if($node){
                                        $node_list[] = $item;
                                    }
                                }
                            }
                        } else {
                            throw new Exception('Unknown state detected...');
                        }
                    } else {
                        $output = [];
                        $command = Core::binary($object) .
                            ' r3m_io/node' .
                            ' create' .
                            ' -class=Account.Role' .
                            ' -name=' . $roles_role['name'] .
                            ' -rank=' . $roles_role['rank'] .
                            ' -permission=' . $roles_role['permission']
                        ;
//                        echo $command . PHP_EOL;
                        exec($command, $output, $code);
                        if($code === 0){
                            $item = Core::object(implode(PHP_EOL, $output), Core::OBJECT_OBJECT);
                            if($item){
                                $node_list[] = $item;
                            }
                        }
                    }
                }
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
                    'transaction' => true,
                    'role' => $node_list
                ];
            }
        }
        return false;
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function account_create_jwt($flags, $options){
        $object = $this->object();
        $url_jwt = $object->config('project.dir.data') . 'Account/Jwt.json';
        if(File::exist($url_jwt)){
            if(property_exists($options, 'force')){
                File::delete($url_jwt);
            } else {
                return false;
            }
        }
        if(!property_exists($options, 'token')){
            $options->token = (object) [];
        }
        $permitted_for = Core::uuid();
        if(!property_exists($options->token, 'private_key')){
            $options->token->private_key = '{{config(\'project.dir.data\')}}Ssl/Token_key.pem';
            //create private key
            if(!File::exist($object->config('project.dir.data') . 'Ssl/Token_key.pem')){
                $command = Core::binary($object) .
                    ' r3m_io/basic' .
                    ' openssl' .
                    ' init' .
                    ' -keyout=' . 'Token_key.pem' .
                    ' -out=' . 'Token_cert.pem'
                ;
                exec($command, $output, $code);
                if($code !== 0){
                    throw new Exception('Error creating private key & certificate' . implode(PHP_EOL, $output) . PHP_EOL);
                }
            }
        }
        if(!property_exists($options->token, 'certificate')){
            $options->token->certificate = '{{config(\'project.dir.data\')}}Ssl/Token_cert.pem';
            //create certificate
        }
        if(!property_exists($options->token, 'passphrase')){
            $options->token->passphrase = '';
        }
        if(!property_exists($options->token, 'issued_at')){
            $options->token->issued_at = 'now';
        }
        if(!property_exists($options->token, 'identified_by')){
            $options->token->identified_by = Core::uuid();
        }
        if(!property_exists($options->token, 'permitted_for')){
            $options->token->permitted_for = $permitted_for;
        }
        if(!property_exists($options->token, 'can_only_be_used_after')){
            $options->token->can_only_be_used_after = 'now';
        }
        if(!property_exists($options->token, 'expires_at')){
            $options->token->expires_at = '+9 hours';
        }
        if(!property_exists($options->token, 'issued_by')){
            $options->token->issued_by = 'R3m.io';
        }
        if(!property_exists($options, 'refresh')){
            $options->refresh = (object) [];
            $options->refresh->token = (object) [];
        }
        if(!property_exists($options->refresh->token, 'private_key')){
            $options->refresh->token->private_key = '{{config(\'project.dir.data\')}}Ssl/RefreshToken_key.pem';
            //create private key
            if(!File::exist($object->config('project.dir.data') . 'Ssl/RefreshToken_key.pem')){
                $command = Core::binary($object) .
                    ' r3m_io/basic' .
                    ' openssl' .
                    ' init' .
                    ' -keyout=' . 'RefreshToken_key.pem' .
                    ' -out=' . 'RefreshToken_cert.pem'
                ;
                exec($command, $output, $code);
                if($code !== 0){
                    throw new Exception('Error creating private key & certificate' . implode(PHP_EOL, $output) . PHP_EOL);
                }
            }
        }
        if(!property_exists($options->refresh->token, 'certificate')){
            $options->refresh->token->certificate = '{{config(\'project.dir.data\')}}Ssl/RefreshToken_cert.pem';
            //create certificate
        }
        if(!property_exists($options->refresh->token, 'passphrase')){
            $options->refresh->token->passphrase = '';
        }
        if(!property_exists($options->refresh->token, 'issued_at')){
            $options->refresh->token->issued_at = 'now';
        }
        if(!property_exists($options->refresh->token, 'identified_by')){
            $options->refresh->token->identified_by = Core::uuid();
        }
        if(!property_exists($options->refresh->token, 'permitted_for')){
            $options->refresh->token->permitted_for = $permitted_for;
        }
        if(!property_exists($options->refresh->token, 'can_only_be_used_after')){
            $options->refresh->token->can_only_be_used_after = 'now';
        }
        if(!property_exists($options->refresh->token, 'expires_at')){
            $options->refresh->token->expires_at = '+48 hours';
        }
        if(!property_exists($options->refresh->token, 'issued_by')){
            $options->refresh->token->issued_by = 'R3m.io';
        }
        File::write($url_jwt, Core::object($options, Core::OBJECT_JSON));
    }

    /**
     * @throws Exception
     */
    public function user_create_login($flags, $options)
    {
        $object = $this->object();
        if(!property_exists($options, 'namespace')){
            throw new Exception('Option namespace required.');
        }
        $parse = $this->parse();
//        $parse->storage('options', $options);
        $dir_template = $object->config('project.dir.package') .
            'R3m' .
            $object->config('ds') .
            'Io' .
            $object->config('ds') .
            'Account' .
            $object->config('ds') .
            'Data' .
            $object->config('ds') .
            'Php' .
            $object->config('ds')
        ;
        $url_template = $dir_template .
            'User' .
            $object->config('extension.php') .
            $object->config('extension.tpl')
        ;
        $data = File::read($url_template);

        $response = $parse->compile($data, $parse->storage());
        d($options);
        ddd($response);
    }
}