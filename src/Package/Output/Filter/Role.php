<?php

namespace Package\R3m\Io\Account\Output\Filter;

use R3m\Io\App;

use R3m\Io\Module\Controller;

class Role extends Controller {

    const DIR = __DIR__ . '/';

    public static function permission(App $object, $response=null): array
    {
        //permission object should stay intact
        return $response;
    }
}