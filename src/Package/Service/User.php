<?php
namespace Package\R3m\Io\Account\Service;



use stdClass;
use DateTime;

//use Entity\User as Entity;


use R3m\Io\App;

use R3m\Io\Module\Core;
use R3m\Io\Module\Data;
use R3m\Io\Module\Database;
use R3m\Io\Module\Handler;
use R3m\Io\Module\Response;

use Exception;

use R3m\Io\Exception\FileWriteException;
use R3m\Io\Exception\ObjectException;
use R3m\Io\Exception\AuthorizationException;
use R3m\Io\Exception\ErrorException;

class User
{
    const BLOCK_EMAIL_COUNT = 5;
    const BLOCK_PASSWORD_COUNT = 5;

    public static function login(App $object): array
    {
        d($object->Request());
        ddd('here we are');
        if(User::is_blocked($object, $object->request('email')) === false){
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
        } else {
            $status = 401;
            Handler::header('Status: ' . $status, $status, true);
            Userlogger::log($object, null, UserLogger::STATUS_BLOCKED);
            throw new ErrorException('User blocked.');
        }
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
        ddd('is.blocked');
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