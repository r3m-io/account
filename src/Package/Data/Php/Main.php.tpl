{{R3M}}
{{$options = options()}}
{{$test = true}}
{{$constant = $options.constant2|default:(object)[
'test1' => [
    'test2' => $test
],
]}}
{{dd($constant)}}