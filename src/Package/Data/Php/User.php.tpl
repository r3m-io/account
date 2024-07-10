{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
{{$use = [
'R3m\Io\App',
'R3m\Io\Module\Response',
'R3m\Io\Module\Controller',
'Exception',
'R3m\Io\Exception\LocateException',
'R3m\Io\Exception\UrlEmptyException',
'R3m\Io\Exception\UrlNotExistException',
]}}
{{$extends = 'Controller'}}
{{$implements = ''}}
{{$constants = {
'DIR' => '__DIR__ . DIRECTORY_SEPARATOR',
'NAME' => $class,
'COMMAND_TOKEN' => 'token',
'COMMAND_INFO' => 'info',
'COMMAND' => [
'{{$class}}::COMMAND_INFO',
'{{$class}}::COMMAND_TOKEN'
],
'DEFAULT_COMMAND' => '{{$class}}::COMMAND_INFO',
'EXCEPTION_COMMAND_PARAMETER' => '{{$command}}',
'EXCEPTION_COMMAND' => 'invalid command (' . ' . "{{$class}}" . '::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL,
}}}
{{dd($constants)}}
{{$traits = [
]}}
<?php
namespace {{$namespace}};
{{for.each($use as $usage)}}
use {{$usage}};
{{/for.each}}

class {{$class}} extends {{$extends}} {
    const DIR = __DIR__ . DIRECTORY_SEPARATOR;
    const NAME = "{{$class}}";

    const COMMAND_TOKEN = 'token';
    const COMMAND_INFO = 'info';
    const COMMAND = [
        {{$class}}::COMMAND_INFO,
        {{$class}}::COMMAND_TOKEN
    ];
    const DEFAULT_COMMAND = {{$class}}::COMMAND_INFO;

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . ' . "{{$class}}" . '::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    const INFO = [
        '{{binary()}} ' . "{{$class}}" . ' token <email>             | Request a login token with e-mail'
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
            $command = {{$class}}::DEFAULT_COMMAND;
        }
        if(!in_array($command, {{$class}}::COMMAND)){
            $exception = str_replace(
                {{$class}}::EXCEPTION_COMMAND_PARAMETER,
                $command,
                {{$class}}::EXCEPTION_COMMAND
            );
            throw new Exception($exception);
        }
        $user = new {{$class}}($object);
        return $user->{$command}();
    }

    private function info(){
        $object = $this->object();
        try {
            $name = {{$class}}::name(__FUNCTION__, __CLASS__, '/');
            $url = {{$class}}::locate($object, $name);
            return {{$class}}::response($object, $url);
        }
        catch (Exception | LocateException | UrlEmptyException | UrlNotExistException $exception) {
            return $exception;
        }
    }
}