{{R3M}}
{{$options = options()}}
{{$test = true}}
{{$constant = $options.constant2|default:(object)[
'test' => [
    'test' => 'test'
],
]}}
{{dd($constant)}}