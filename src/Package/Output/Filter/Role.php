<?php

namespace Package\R3m\Io\Account\Output\Filter;

use R3m\Io\App;

use R3m\Io\Module\Controller;

class Role extends Controller {

    const DIR = __DIR__ . '/';

    public static function permission(App $object, $response=null): array
    {
        //permission array should stay intact
        return $response;
    }

    public static function user(App $object, $response=null): array
    {
        //permission array can be compacted to array
        if(is_array($response)){
            foreach($response as $nr => $role){
                if(
                    property_exists($role, 'permission') &&
                    is_array($role->permission)
                ){
                    foreach($role->permission as $permission_nr => $permission){
                        if(property_exists($permission, 'name')){
                            $role->permission[$permission_nr] = $permission->name;
                        }
                    }
                }
            }
        }
        return $response;
    }
}