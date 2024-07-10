{{R3M}}
{{$options = options()}}
{{dd($options)}}
{{$namespace = $options.namespace}}
{{$class = $options.class}}
<?php
namespace {{$namespace}};
{{for.each($use as $usage)}}
use {{$usage}};
{{/for.each}}

class {{$class}} extends {{$extends}} {

{{for.each($constants as $constant)}}
    {{for.each($constant as $property => $value)}}
    const {{$property}} = {{$value}};
    {{/for.each}}
{{/for.each}}

{{for.each($traits as $trait)}}
    use {{$trait}};
{{/for.each}}
{{for.each($functions as $function)}}
    {{$function}}
{{/for.each}}
}