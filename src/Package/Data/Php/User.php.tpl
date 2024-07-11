{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
{{$extends = $options.extends}}
{{$implements = $options.implements}}
{{$use = $options.use}}
{{$constants = $options.constant}}
{{$traits = $options.trait}}
{{$functions = $options.function}}
<?php
namespace {{$namespace}};

{{for.each($use as $usage)}}
use {{$usage}};
{{/for.each}}

{{if($implements && $extends)}}
class {{$class}} extends {{$extends}} implements {{$implements}} {
{{elseif($implements)}}
class {{$class}} implements {{$implements}} {
{{elseif($extends)}}
class {{$class}} extends {{$extends}} {
{{else}}
class {{$class}} {
{{/if}}

{{for.each($constants as $property => $value)}}
{{if(is.array($value))}}
    const {{$property}} = [
    {{implode(',' + "\n    ", $value)}}

    ];
{{else}}
    const {{$property}} = {{$value}};
{{/if}}
{{/for.each}}

{{for.each($traits as $trait)}}
    use {{$trait}};
{{/for.each}}

{{for.each($functions as $function)}}
{{if($function.static)}}
    {{$function.type}} static function {{$function.name}}({{implode(', ', $function.argument)}}) {
{{else}}
    {{$function.type}} function {{$function.name}}({{implode(', ', $function.argument)}}) {
{{/if}}

{{dd($function)}}

{{implode("\n        ", $function.body)}}

    }

{{/for.each}}
}