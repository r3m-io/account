{{R3M}}
{{$options = options()}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
{{$trait = $options.trait}}
{{$extends = $options.extends}}
{{$implements = $options.implements}}
{{$use = $options.use}}
{{$constants = $options.constant}}
{{$traits = $options.trait_use}}
{{$functions = $options.function}}
<?php
namespace {{$namespace}};

{{for.each($use as $usage)}}
use {{$usage}};
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

{{for.each($functions as $function)}}
{{if($function.doc_comment)}}
    {{implode("\n    ", $function.doc_comment)}}

{{/if}}
{{if($function.throw)}}
    /**
    {{for.each($function.throw as $throw)}}
     * @throws {{$throw}}

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
{{if($class || $trait)}}
}
{{/if}}