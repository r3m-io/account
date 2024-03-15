<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\Config;

use R3m\Io\Module\Core;
use R3m\Io\Module\File;
use R3m\Io\Module\Dir;

use Exception;

trait Main {

    public function account_create_default($flags, $options){
        d($flags);
        d($options);
    }
}