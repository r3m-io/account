<?php

namespace Package\R3m\Io\Account\Output\Filter;

use R3m\Io\App;

use R3m\Io\Module\Controller;

class User extends Controller {

    const DIR = __DIR__ . '/';

    public static function role_permission(App $object, $response=null): array
    {
        $result = [];
        $is_result = false;
        trace();
        d($response);
        if(
            !empty($response) &&
            is_array($response)
        ){
            /*
            foreach($response as $nr => $record){
                if(
                    is_object($record) &&
                    property_exists($record, 'name')
                ){
                    $is_result = true;
                    $result[] = $record->name;
                }
            }
            */
        }
        if($is_result){
            return $result;
        }
        return $response;
    }
}