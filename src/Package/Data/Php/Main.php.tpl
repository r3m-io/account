{{R3M}}
{{$options = options()}}
{{$test = true}}


{{$test2 = (object) [
'1' => 'test',
'2' => (object) [
'test2',
'test3',
],
'nice' => 'very-nice'
]}}


{{$constant = $options.constant2|default:(object) [
'test1' =>  (object) [
    'test2' + 'test7' => (clone) $test2,
    'test3' => 'test4',
    'test7' => [
        0,
        1
    ]
],
'test5' => 'test6',
'test8' => $test
]}}
{{d($test2)}}
{{d($constant)}}