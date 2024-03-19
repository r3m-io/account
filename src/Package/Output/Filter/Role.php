<?php

namespace Package\R3m\Io\Account\Output\Filter;

use R3m\Io\App;

use R3m\Io\Module\Controller;

class Role extends Controller {

    const DIR = __DIR__ . '/';

    public static function permission(App $object, $response=null): object
    {
        $result = [];
        if(
            !empty($response) &&
            is_array($response)
        ){
            ddd($response);
            foreach($response as $nr => $record){
                if(
                    is_array($record) &&
                    array_key_exists('name', $record) &&
                    array_key_exists('environment', $record)
                ){
                    if($record['environment'] === '*'){
                        $result[$record['name']] = $record;
                    } else {
                        if(!array_key_exists($record['name'], $result)){
                            $result[$record['name']] = [];
                        }
                        $result[$record['name']][$record['environment']] = $record;
                    }
                }
                elseif(
                    is_object($record) &&
                    property_exists($record, 'name') &&
                    property_exists($record, 'environment')
                ){
                    if($record->environment === '*'){
                        $result[$record->name] = $record;
                    } else {
                        if(!array_key_exists($record->name, $result)){
                            $result[$record->name] = [];
                        }
                        $result[$record->name][$record->environment] = $record;
                    }
                }
            }
        }
        return (object) $result;
    }

}