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
{{$user_traits = $options.user_trait_use}}
{{$user_use = $options.user_use}}
{{$user_functions = $options.user_function}}
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
{{for.each($user_traits as $user_trait_use)}}
    use {{$user_trait_use}};
{{/for.each}}
{{$private = Package.R3m.Io.Account:Php:php.variable.define($private, 'private')}}
{{implode("\n", $private)}}
{{$protected = Package.R3m.Io.Account:Php:php.variable.define($protected, 'protected')}}
{{implode("\n", $protected)}}
{{$public = Package.R3m.Io.Account:Php:php.variable.define($public, 'public')}}
{{implode("\n", $public)}}


{{$functions = Package.R3m.Io.Account:Php:php.functions.define($functions)}}
{{implode("\n", $functions)}}


{{for.each($functions as $function)}}
{{if($function.doc_comment)}}
    {{implode("\n    ", $function.doc_comment)}}

{{/if}}
{{if($function.throw)}}
    /**
{{for.each($function.throw as $throw_nr => $throw)}}
     * @throws {{$throw}}
{{if(!is.empty($function.throw[$throw_nr + 1]))}}


{{/if}}
{{/for.each}}

     */
{{/if}}
{{if($function.attribute)}}
    {{implode("\n    ", $function.attribute)}}

{{/if}}
{{if($function.static && $function.return_type)}}
    {{$function.type}} static function {{$function.name}}({{implode(', ', $function.argument)}}) : {{implode('|', $function.return_type)}}

{{elseif($function.static)}}
    {{$function.type}} static function {{$function.name}}({{implode(', ', $function.argument)}})
{{elseif($function.return_type)}}
    {{$function.type}} function {{$function.name}}({{implode(', ', $function.argument)}}) : {{implode('|', $function.return_type)}}

{{else}}
    {{$function.type}} function {{$function.name}}({{implode(', ', $function.argument)}})
{{/if}}
    {
        {{implode("\n        ", $function.body)}}

    }


{{/for.each}}
{{for.each($user_functions as $user_function)}}
{{if($user_function.doc_comment)}}
    {{implode("\n    ", $user_function.doc_comment)}}

{{/if}}
{{if($user_function.throw)}}
    /**
{{for.each($user_function.throw as $user_throw_nr => $user_throw)}}
     * @throws {{$user_throw}}
{{if(!is.empty($user_function.throw[$user_throw_nr + 1]))}}


{{/if}}
{{/for.each}}

     */
{{/if}}
{{if($user_function.attribute)}}
    {{implode("\n    ", $user_function.attribute)}}

{{/if}}
{{if($user_function.static && $user_function.return_type)}}
    {{$user_function.type}} static function {{$user_function.name}}({{implode(', ', $user_function.argument)}}) : {{implode('|', $user_function.return_type)}}

{{elseif($user_function.static)}}
    {{$user_function.type}} static function {{$user_function.name}}({{implode(', ', $user_function.argument)}})
{{elseif($user_function.return_type)}}
    {{$user_function.type}} function {{$user_function.name}}({{implode(', ', $user_function.argument)}}) : {{implode('|', $user_function.return_type)}}

{{else}}
    {{$user_function.type}} function {{$user_function.name}}({{implode(', ', $user_function.argument)}})
{{/if}}
    {
        {{implode("\n        ", $user_function.body)}}

    }


{{/for.each}}
{{if($class || $trait)}}
}
{{/if}}