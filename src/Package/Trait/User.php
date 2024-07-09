<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\Module\Cli;
use R3m\Io\Module\Handler;
use R3m\Io\Module\Response;
use Package\R3m\Io\Account\Service\User as Service;

use Exception;

use Doctrine\ORM\Exception\ORMException;

use R3m\Io\Exception\FileWriteException;
use R3m\Io\Exception\ObjectException;
use R3m\Io\Exception\AuthorizationException;

trait User
{

    /**
     * @throws Exception
     */
    private function token(){
        $object = $this->object();
        if(Handler::method() === Handler::METHOD_CLI){
            $email = $object->parameter($object, __FUNCTION__, 1);
            $data = [];
            $data[] = Cli::tput('color', Cli::COLOR_GREEN);
            $data[] = Service::token($object, $email);
            $data[] = Cli::tput('reset');
            return new Response(
                $data,
                Response::TYPE_CLI
            );
        }
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function login(){
        $object = $this->object();
        if (Handler::method() === 'POST') {
            $data = Service::login($object);
            return new Response(
                $data,
                Response::TYPE_JSON
            );
        }
    }

    /**
     * @throws AuthorizationException
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function current()
    {
        $object = $this->object();
        if (Handler::method() === 'GET') {
            $data = Service::current($object);
            return new Response(
                $data,
                Response::TYPE_JSON
            );
        }
    }

    /**
     * @throws AuthorizationException
     * @throws Exception
     */
    public function refresh_token(){
        $object = $this->object();
        if (Handler::method() === 'GET') {
            $data =  Service::refresh_token($object);
            return new Response(
                $data,
                Response::TYPE_JSON
            );
        }
    }

}