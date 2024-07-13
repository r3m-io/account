{{R3M}}
{{$options = options()}}
{{$test = true}}
{{$constant = $options.constant2|default:(object)[
'test1' => [
    'test2' + 'test7' => $test,
    'test3' => 'test4'
],
'test5' => 'test6'
]}}
{{dd($constant)}}