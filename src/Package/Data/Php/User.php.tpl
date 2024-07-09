{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
<?php
namespace {{$namespace}};


use R3m\Io\App;

use R3m\Io\Module\Cli;
use R3m\Io\Module\File;
use R3m\Io\Module\Handler;
use R3m\Io\Module\Response;
use R3m\Io\Module\Controller;
use Package\R3m\Io\Account\Service\User as Service;

use Exception;

use Doctrine\ORM\Exception\ORMException;

use R3m\Io\Exception\FileWriteException;
use R3m\Io\Exception\ObjectException;
use R3m\Io\Exception\AuthorizationException;
use R3m\Io\Exception\LocateException;
use R3m\Io\Exception\UrlEmptyException;
use R3m\Io\Exception\UrlNotExistException;

class User extends Controller {
    const DIR = __DIR__ . DIRECTORY_SEPARATOR;
    const NAME = 'User';

    const COMMAND_TOKEN = 'token';
    const COMMAND_INFO = 'info';
    const COMMAND = [
        User::COMMAND_INFO,
        User::COMMAND_TOKEN
    ];
    const DEFAULT_COMMAND = User::COMMAND_INFO;

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . User::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    const INFO = [
        '{{binary()}} user token <email>             | Request a login token with e-mail'
    ];

    use Package\R3m\Io\Account\Module\User;

    /**
    * @throws Exception
    */
    public static function command(App $object){
        $command = $object->parameter($object, __CLASS__, 1);
        if($command === null){
            $command = User::DEFAULT_COMMAND;
        }
        if(!in_array($command, User::COMMAND)){
            $exception = str_replace(
                User::EXCEPTION_COMMAND_PARAMETER,
                $command,
                User::EXCEPTION_COMMAND
            );
            throw new Exception($exception);
        }
        $user = new User();
        return $user->{$command}($object);
    }

    private function info(App $object){
        try {
            $name = User::name(__FUNCTION__, __CLASS__, '/');
            $url = User::locate($object, $name);
            return User::response($object, $url);
        } catch (Exception | LocateException | UrlEmptyException | UrlNotExistException $exception) {
            return $exception;
        }
    }
}
