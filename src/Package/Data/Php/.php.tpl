{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
{{$trait = $options.trait}}
{{$extends = $options.extends}}
{{$implements = $options.implements}}
{{$use = $options.use}}
{{$constants = $options.constant}}
{{$private = $options.private}}
{{$protected = $options.protected}}
{{$public = $options.public}}
{{$traits = $options.trait_use}}
{{$functions = $options.function}}
{{$user.private = $options.user.private}}
{{$user.protected = $options.user.protected}}
{{$user.public = $options.user.public}}
{{$user.traits = $options.user.trait_use}}
{{$user.use = $options.user.use}}
{{$user.functions = $options.user.function}}
<?php
namespace {{$namespace}};

{{for.each($use as $usage)}}
use {{$usage}};
{{/for.each}}
{{for.each($user_use as $user_usage)}}
use {{$user_usage}};
{{/for.each}}

{{if(!$class && !$trait)}}
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

{{for.each($constants as $property => $value)}}
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
{{$private = Package.R3m.Io.Account:Php:php.variable.define($private, 'private')}}
{{implode("\n", $private)}}
{{$protected = Package.R3m.Io.Account:Php:php.variable.define($protected, 'protected')}}
{{implode("\n", $protected)}}
{{$public = Package.R3m.Io.Account:Php:php.variable.define($public, 'public')}}
{{implode("\n", $public)}}
{{$user.private = Package.R3m.Io.Account:Php:php.variable.define($user.private, 'private')}}
{{implode("\n", $user.private)}}
{{$user.protected = Package.R3m.Io.Account:Php:php.variable.define($user.protected, 'protected')}}
{{implode("\n", $user.protected)}}
{{$user.public = Package.R3m.Io.Account:Php:php.variable.define($user.public, 'public')}}
{{implode("\n", $user.public)}}

{{$functions = Package.R3m.Io.Account:Php:php.function.define($functions)}}
{{implode("\n", $functions)}}
{{$user_functions = Package.R3m.Io.Account:Php:php.function.define($user_functions)}}
{{implode("\n", $user_functions)}}

{{if($class || $trait)}}
}
{{/if}}