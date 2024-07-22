{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
{{$trait = $options.trait}}
{{$extends = $options.extends}}
{{$implements = $options.implements|default:[]}}
{{$use = $options.use|default:[]}}
{{$constant = $options.constant|default:[]}}
{{$variable.private = $options.variable.private|default:[]}}
{{$variable.protected = $options.variable.protected|default:[]}}
{{$variable.public = $options.variable.public|default:[]}}
{{$traits = $options.trait_use|default:[]}}
{{$function = $options.function|default:[]}}
{{$user.variable.private = $options.user.variable.private|default:[]}}
{{$user.variable.protected = $options.user.variable.protected|default:[]}}
{{$user.variable.public = $options.user.variable.public|default:[]}}
{{$user.traits = $options.user.trait_use|default:[]}}
{{$user.use = $options.user.use|default:[]}}
{{$user.function = $options.user.function|default:[]}}
{{$user.constant = $options.user.constant|default:[]}}
<?php
namespace {{$namespace}};

{{for.each($use as $usage)}}
use {{$usage}};
{{/for.each}}
{{for.each($user.use as $user.usage)}}
use {{$user.usage}};
{{/for.each}}

{{if(
is.empty($class) &&
is.empty($trait)
)}}
{{elseif($trait)}}
trait {{$trait}} {
{{else}}
{{if($implements && $extends)}}
class {{$class}} extends {{$extends}} implements {{implode(', ', $implements)}} {
{{elseif($implements)}}
class {{$class}} implements {{implode(', ', $implements)}} {
{{elseif($extends)}}
class {{$class}} extends {{$extends}} {
{{else}}
class {{$class}} {
{{/if}}
{{/if}}
{{for.each($constant as $property => $value)}}
{{if(is.array($value))}}
    const {{$property}} = [
        {{implode(',' + "\n        ", $value)}}

    ];
{{else}}
    const {{$property}} = {{$value}};
{{/if}}
{{/for.each}}
{{for.each($user.constant as $property => $value)}}
{{if(is.array($value))}}
    const {{$property}} = [
        {{implode(',' + "\n        ", $value)}}

    ];
{{else}}
    const {{$property}} = {{$value}};
{{/if}}
{{/for.each}}

{{for.each($traits as $trait_use)}}
    use {{$trait_use}};
{{/for.each}}
{{for.each($user.traits as $user.trait.use)}}
    use {{$user.trait.use}};
{{/for.each}}
{{$variable.private = Package.R3m.Io.Account:Php:php.variable.define($variable.private, 'private')}}
{{implode("\n", $variable.private)}}
{{$user.variable.private = Package.R3m.Io.Account:Php:php.variable.define($user.variable.private, 'private')}}
{{implode("\n", $user.variable.private)}}

{{$variable.protected = Package.R3m.Io.Account:Php:php.variable.define($variable.protected, 'protected')}}
{{implode("\n", $variable.protected)}}
{{$user.variable.protected = Package.R3m.Io.Account:Php:php.variable.define($user.variable.protected, 'protected')}}
{{implode("\n", $user.variable.protected)}}

{{$variable.public = Package.R3m.Io.Account:Php:php.variable.define($variable.public, 'public')}}
{{implode("\n", $variable.public)}}
{{$user.variable.public = Package.R3m.Io.Account:Php:php.variable.define($user.variable.public, 'public')}}
{{implode("\n", $user.variable.public)}}

{{$function = Package.R3m.Io.Account:Php:php.function.define($function)}}
{{implode("\n", $function)}}
{{$user.function = Package.R3m.Io.Account:Php:php.function.define($user.function)}}
{{implode("\n", $user.function)}}

{{if($class || $trait)}}
}
{{/if}}