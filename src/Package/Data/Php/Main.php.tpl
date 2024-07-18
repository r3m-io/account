{{R3M}}
{{$options = options()}}
{{$test3 = 4 + 3}}
{{$test4 = null}}
{{$test5 = {{$test3}} + 1}}
{{$test = $options.null|default: 1 + {{$test3}} + ($test4)}}
{{d($test)}}