{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
<?php
namespace {{$namespace}};

use R3m\Io\App;

use R3m\Io\Module\Response;
use R3m\Io\Module\Controller;

use Exception;

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

    use Package\R3m\Io\Account\Trait\User;
    use Package\R3m\Io\Account\Trait\App;

    public function __construct(App $object){
        $this->object($object);
    }

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
        $user = new User($object);
        return $user->{$command}();
    }

    private function info(){
        $object = $this->object();
        try {
            $name = User::name(__FUNCTION__, __CLASS__, '/');
            $url = User::locate($object, $name);
            return User::response($object, $url);
        }
        catch (Exception | LocateException | UrlEmptyException | UrlNotExistException $exception) {
            return $exception;
        }
    }
}
