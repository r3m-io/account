{{R3M}}
{{$options = options()}}
{{$options.class = null}}
{{$options.trait = null}}
<?php
namespace {{$options.namespace}};

{{if(is.array($options.use))}}
{{for.each($options.use as $usage)}}
use {{$usage}};
{{/for.each}}
{{/if}}
{{if(is.array($options.user.use))}}
{{for.each($options.user.use as $user.usage)}}
use {{$user.usage}};
{{/for.each}}
{{/if}}

{{if(!$options.class && !$options.trait)}}
{{elseif($options.trait)}}
trait {{$options.trait}} {
{{else}}
{{if($options.implements && $options.extends)}}
class {{$options.class}} extends {{$options.extends}} implements {{implode(', ', $options.implements)}} {
{{elseif($options.implements)}}
class {{$options.class}} implements {{implode(', ', $options.implements)}} {
{{elseif($options.extends)}}
class {{$options.class}} extends {{$options.extends}} {
{{else}}
class {{$options.class}} {
{{/if}}
{{/if}}

{{if(is.array($options.constant))}}
{{for.each($options.constant as $property => $value)}}
{{if(is.array($value))}}
    const {{$property}} = [
        {{implode(',' + "\n        ", $value)}}

    ];
{{else}}
    const {{$property}} = {{$value}};
{{/if}}
{{/for.each}}

{{/if}}
{{if(is.array($options.trait_use))}}
{{for.each($options.trait_use as $trait_use)}}
    use {{$trait_use}};
{{/for.each}}
{{/if}}
{{if(is.array($options.user.trait_use))}}
{{for.each($options.user.trait_use as $user.trait.use)}}
    use {{$user.trait.use}};
{{/for.each}}
{{/if}}
{{$private = Package.R3m.Io.Account:Php:php.variable.define($options.private, 'private')}}
{{implode("\n", $private)}}
{{$user.private = Package.R3m.Io.Account:Php:php.variable.define($options.user.private, 'private')}}
{{implode("\n", $user.private)}}
{{$protected = Package.R3m.Io.Account:Php:php.variable.define($options.protected, 'protected')}}
{{implode("\n", $protected)}}
{{$user.protected = Package.R3m.Io.Account:Php:php.variable.define($options.user.protected, 'protected')}}
{{implode("\n", $user.protected)}}
{{$public = Package.R3m.Io.Account:Php:php.variable.define($options.public, 'public')}}
{{implode("\n", $public)}}
{{$user.public = Package.R3m.Io.Account:Php:php.variable.define($options.user.public, 'public')}}
{{implode("\n", $user.public)}}


{{$function = Package.R3m.Io.Account:Php:php.function.define($options.function)}}
{{implode("\n", $function)}}
{{$user.function = Package.R3m.Io.Account:Php:php.function.define($options.user.function)}}
{{implode("\n", $user.function)}}
{{if($options.class || $options.trait)}}
}
{{/if}}