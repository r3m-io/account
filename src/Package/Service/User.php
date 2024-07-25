<?php
namespace Package\R3m\Io\Account\Service;

use R3m\Io\Module\Data as Storage;
use stdClass;
use DateTime;

//use Entity\User as Entity;


use R3m\Io\App;

use R3m\Io\Module\Core;
use R3m\Io\Module\Data;
use R3m\Io\Module\Database;
use R3m\Io\Module\Handler;
use R3m\Io\Module\Response;

use R3m\Io\Node\Model\Node;

use Exception;

use R3m\Io\Exception\FileWriteException;
use R3m\Io\Exception\ObjectException;
use R3m\Io\Exception\AuthorizationException;
use R3m\Io\Exception\ErrorException;

class User
{
    const BLOCK_EMAIL_COUNT = 5;
    const BLOCK_PASSWORD_COUNT = 5;

    /**
     * @throws ObjectException
     * @throws ErrorException
     * @throws FileWriteException
     * @throws Exception
     */
    public static function login(App $object): mixed
    {
        $name = 'Account.User';
        $options['function'] = __FUNCTION__;
        if(User::is_blocked($object, $object->request('email')) === false){
            /*
            $url = $object->config('project.dir.node') . 'Data' .
                $object->config('ds') .
                'Account.User' .
                $object->config('extension.json')
            ;
            */
            $node = new Node($object);
            $record = $node->record(
                $name,
                $node->role_system(),
                [
                    'where' => [
                        [
                            'value' => $object->request('email'),
                            'attribute' => 'email',
                            'operator' => '==='
                        ],
                        [
                            'value' => 1,
                            'attribute' => 'is.active',
                            'operator' => '>='
                        ]
                    ],
                ]
            );
            if(
                $record &&
                array_key_exists('node', $record) &&
                property_exists($record['node'], 'uuid')
            ){
                $password = $object->request('password');
                $verify = password_verify($password, $record['node']->password);
                if(empty($verify)){
                    $status = 401;
                    Handler::header('Status: ' . $status, $status, true);
//                    Userlogger::log($object, $node, UserLogger::STATUS_INVALID_PASSWORD);
                    throw new ErrorException('Invalid e-mail-password.');
                }
//                Userlogger::log($object, $node, UserLogger::STATUS_SUCCESS);
//                $array = User::getTokens($object, $record['node']);
//                $data = [];
//                $data['node'] = $array;
                $record = $node->record(
                    $name,
                    $node->role_system(),
                    [
                        'where' => [
                            [
                                'value' => $object->request('email'),
                                'attribute' => 'email',
                                'operator' => '==='
                            ],
                            [
                                'value' => 1,
                                'attribute' => 'is.active',
                                'operator' => '>='
                            ]
                        ],
                        'relation' => true
                    ]
                );
                if($record){
                    unset($record['node']->password);
                    $record['node'] = User::getTokens($object, $record['node']);
                    $node = new Node($object);
                    $data = new Storage();
                    $data->data($record['node']);
                    $data->set('#class', $name);
                    $expose = $node->expose_get(
                        $object,
                        $name,
                        $name . '.' . $options['function'] . '.output'
                    );
                    ddd($record);
                    /*
                    $result = $node->record(
                        'Account.Role',
                        $node->role_system(),
                        [
                            'where' => [
                                [
                                    'value' => $record['node']->role,
                                    'attribute' => 'uuid',
                                    'operator' => '==='
                                ]
                            ]
                        ]
                    )
                    */

                    if (
                        $expose &&
                        $role
                    ) {
                        $node = $node->expose(
                            $node,
                            $expose,
                            $name,
                            $options['function'],
                            $role
                        );
                        $record = $node->data();
                    }

                    return $record;
                }

            } else {
                //mysql user
                /*
                $entityManager = Database::entityManager($object);
                $repository = $entityManager->getRepository(Entity::class);
                $node = $repository->findOneBy([
                    'email' => $object->request('email'),
                    'isActive' => 1
                ]);
                if($node) {
                    $password = $object->request('password');
                    $verify = password_verify($password, $node->getPassword());
                    if(empty($verify)){
                        $status = 401;
                        Handler::header('Status: ' . $status, $status, true);
                        Userlogger::log($object, $node, UserLogger::STATUS_INVALID_PASSWORD);
                        throw new ErrorException('Invalid e-mail-password.');
                    }
                    Userlogger::log($object, $node, UserLogger::STATUS_SUCCESS);
                    $array = User::getTokens($object, $node);
                    $data = [];
                    $data['node'] = $array;
                    return $data;
                } else {
                    $status = 401;
                    Handler::header('Status: ' . $status, $status, true);
                    Userlogger::log($object, null, UserLogger::STATUS_INVALID_EMAIL);
                    throw new ErrorException('Invalid e-mail-password.');
                }
                */
                $status = 401;
                Handler::header('Status: ' . $status, $status, true);
//                Userlogger::log($object, null, UserLogger::STATUS_INVALID_EMAIL);
                throw new ErrorException('Invalid e-mail-password.');
            }
        } else {
            $status = 401;
            Handler::header('Status: ' . $status, $status, true);
//            Userlogger::log($object, null, UserLogger::STATUS_BLOCKED);
            throw new ErrorException('User blocked.');
        }
        return [];
    }

    /**
     * @throws Exception
     */
    private static function getTokens(App $object, $record): mixed
    {
        $configuration = Jwt::configuration($object);
        $options = [];
        $options['user'] = $record;
        $token = Jwt::get($object, $configuration, $options);
        $token = $token->toString();
        $options['refresh'] = true;
        $configuration = Jwt::configuration($object, $options);
        $refreshToken = Jwt::refresh_get($object, $configuration, $options);
        $refreshToken = $refreshToken->toString();
        $encrypted_refreshToken = sha1($refreshToken);

        $record->token = $token;
        $record->refresh_token = $refreshToken;

        $node = new Node($object);
        $node->patch(
            'Account.User',
            $node->role_system(),
            [
                'uuid' => $record->uuid,
                'refresh_token' => $encrypted_refreshToken
            ]
        );
        return $record;
    }

    /**
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws ErrorException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws FileWriteException
     * @throws ObjectException
     * @throws Exception
     */
    public static function is_blocked(App $object, $email=''): bool
    {
        d('later');
        return false;





        $entityManager = Database::entityManager($object);
        if(!$entityManager){
            throw new ErrorException('Entity manager not found.');
        }
        $repository = $entityManager->getRepository(Entity::class);
        $node = $repository->findOneBy(['email' => $email]);
        if($node){
            $count = UserLogger::count($object, $node, UserLogger::STATUS_INVALID_PASSWORD);
            if($count >= User::BLOCK_PASSWORD_COUNT){
                Userlogger::log($object, $node, UserLogger::STATUS_BLOCKED);
                return true;
            }
        } else {
            $count = UserLogger::count($object, null, UserLogger::STATUS_INVALID_EMAIL);
            if($count >= User::BLOCK_EMAIL_COUNT){
                Userlogger::log($object, $node, UserLogger::STATUS_BLOCKED);
                return true;
            }
        }
        return false;
    }

}