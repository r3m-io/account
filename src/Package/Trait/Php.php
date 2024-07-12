<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\App;

use R3m\Io\Module\Core;
use R3m\Io\Module\File;

use R3m\Io\Node\Model\Node;

use Exception;
trait Php
{

    public function php_function_define($array=[]){
        $lines = [];
        foreach($array as $function){
            if(!is_object($function)){
                continue;
            }
            if (!property_exists($function, 'name')) {
                continue;
            }
            if (!property_exists($function, 'type')) {
                continue;
            }
            if(
                property_exists($function, 'doc_comment') &&
                !is_scalar($function->doc_comment) &&
                !empty($function->doc_comment)
            ){
                $lines[] = '';
                $lines[] = '    /**';
                foreach($function->doc_comment as $doc_comment){
                    $lines[] ='     * ' . $doc_comment;
                }
                $lines[] ='     */';
            }
            if(
                property_exists($function, 'throw') &&
                !is_scalar($function->throw) &&
                !empty($function->throw)
            ){
                $lines[] = '';
                $lines[] = '    /**';
                foreach($function->throw as $throw){
                    $lines[] ='     * @throws ' . $throw;
                }
                $lines[] ='    */';
            }
            if(
                property_exists($function, 'attribute') &&
                !is_scalar($function->attribute) &&
                !empty($function->attribute)
            ){
                foreach($function->attribute as $attribute){
                    $lines[] = '    ' . $attribute;
                }
            }
            if(
                property_exists($function, 'static') &&
                $function->static === true
            ){
                $header = '    ' . $function->type . ' static function ' . $function->name . '(';
            } else {
                $header = '    ' . $function->type . ' function ' . $function->name . '(';
            }
            $type = '';
            $length = 0;
            if(property_exists($function, 'return_type')){
                $return_type = $function->return_type;
                $type = ' : ' . implode(' | ', $return_type);
                $length += strlen($type);
                if($length > 10){
                    $type = ' : ' . PHP_EOL . '    ' .implode(' |' . PHP_EOL . '    ', $return_type);
                }
            }
            if(
                property_exists($function, 'argument')
            ){
                $arguments = [];
                $length += strlen($header);
                foreach($function->argument as $argument){
                    if(!is_object($argument)){
                        continue;
                    }
                    if(!property_exists($argument, 'name')){
                        continue;
                    }
                    if(!property_exists($argument, 'type')){
                        continue;
                    }
                    if(property_exists($argument, 'value')){
                        $line = $argument->type . ' $' . $argument->name . ' = ' . $argument->value;
                    } else {
                        $line = $argument->type . ' $' . $argument->name;
                    }
                    $length += strlen($line);
                    $arguments[] = $line;
                }
                if($length > 79){
                    $header .= PHP_EOL .
                        '        ' .
                        implode(
                            ',' .
                            PHP_EOL .
                            '        ',
                            $arguments
                        ) .
                        PHP_EOL .
                        '        '
                    ;
                } else {
                    $header .= implode(',         ', $arguments);
                }
            }
            $header .= ')';
            $lines[] = $header . $type;
            $lines[] = '    {';
            if(property_exists($function, 'body')){
                $lines[] = '        ' . implode("\n        ", $function->body);
            }
            $lines[] = '    }';
            $lines[] = '';
        }
        return $lines;
    }

    public function php_variable_define($array=[], $type='private'): array
    {
        $lines = [];
        foreach ($array as $variable) {
            if (!is_object($variable)) {
                continue;
            }
            if (!property_exists($variable, 'name')) {
                continue;
            }
            if (
                property_exists($variable, 'doc_comment') &&
                !is_scalar($variable->doc_comment) &&
                !empty($variable->doc_comment)
            ) {
                $lines[] = '';
                $lines[] = '    /**';
                foreach($variable->doc_comment as $doc_comment){
                    $lines[] ='     * ' . $doc_comment;
                }
                $lines[] ='     */';
            }
            $line = '    ' . $type . ' ';
            if (property_exists($variable, 'static')) {
                $line .= 'static ';
            }
            if (property_exists($variable, 'type')) {
                $line .= $variable->type . ' ';
            }
            $line .= '$' . $variable->name;
            if (!property_exists($variable, 'value')) {
                $line .= ';';
            } else {
                $line .= ' = ';
                $line .= $this->php_variable_define_value($variable->value);
            }
            $lines[] = $line;
        }
        return $lines;
    }

    public function php_variable_define_value($value=null, $indent=1): string
    {
        $result = '';
        if (is_null($value)) {
            $result = 'null;';
        } elseif ($value === true) {
            $result = 'true;';
        } elseif ($value === false) {
            $result = 'false;';
        } elseif (is_array($value)) {
            if (empty($value)) {
                $result = '[];';
            } else {
                $result = '[' . PHP_EOL;
                $indent++;
                foreach ($value as $key => $val) {
                    if (is_numeric($key)) {
                        if (is_null($val)) {
                            $result .= str_repeat(' ', $indent * 4) . 'null,' . PHP_EOL;
                        } elseif ($val === true) {
                            $result .= str_repeat(' ', $indent * 4) . 'true,' . PHP_EOL;
                        } elseif ($val === false) {
                            $result .= str_repeat(' ', $indent * 4) . 'false,' . PHP_EOL;
                        } elseif (is_array($val)) {
                            $result .= $this->php_variable_define_value($val, ++$indent);
                        } else {
                            $result .= str_repeat(' ', $indent * 4) . $val . ',' . PHP_EOL;
                        }
                    } else {
                        $result .= str_repeat(' ', $indent * 4) . $key . ' => ' . $val . ',' . PHP_EOL;
                    }
                }
                $indent--;
                $result .= str_repeat(' ', $indent * 4) . '];';
            }
        } else {
            $result .=  $value . ';';
        }
        return $result;
    }

}