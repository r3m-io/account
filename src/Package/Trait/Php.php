<?php
namespace Package\R3m\Io\Account\Trait;

use R3m\Io\App;

use R3m\Io\Module\Core;
use R3m\Io\Module\File;

use R3m\Io\Node\Model\Node;

use Exception;
trait Php
{

    public function php_variable_define($array = [], $type = 'private'): array
    {
        $lines = [];
        foreach ($array as $variable) {
            if (!is_object($variable)) {
                continue;
            }
            if (!property_exists($variable, 'name')) {
                continue;
            }
            if (property_exists($variable, 'doc_comment')) {
                $lines[] = '/**' . PHP_EOL;
                foreach($variable->doc_comment as $doc_comment){
                    $lines[] =' * ' . $doc_comment . PHP_EOL;
                }
                $lines[] =' */' . PHP_EOL;
            }
            $line = $type . ' ';
            if (property_exists($variable, 'static')) {
                $line .= 'static ';
            }
            if (property_exists($variable, 'type')) {
                $line .= $variable->type . ' ';
            }
            $line .= '$' . $variable->name;
            if (!property_exists($variable, 'value')) {
                $line .= ';' . PHP_EOL;
            } else {
                $line .= ' = ';
                $line .= $this->php_variable_define_value($variable->value);
            }
            $lines[] = $line;
        }
        return $lines;
    }

    public function php_variable_define_value($value=null): string
    {
        $result = '';
        if (is_null($value)) {
            $result = 'null;' . PHP_EOL;
        } elseif ($value === true) {
            $result = 'true;' . PHP_EOL;
        } elseif ($value === false) {
            $result = 'false;' . PHP_EOL;
        } elseif (is_array($value)) {
            if (empty($value)) {
                $result = '[];';
            } else {
                $result = '[' . PHP_EOL;
                foreach ($value as $key => $val) {
                    if (is_numeric($key)) {
                        if (is_null($val)) {
                            $result .= '    null,' . PHP_EOL;
                        } elseif ($val === true) {
                            $result .= '    true,' . PHP_EOL;
                        } elseif ($val === false) {
                            $result .= '    false,' . PHP_EOL;
                        } elseif (is_array($val)) {
                            $result .= $this->php_variable_define_value($val);
                        } else {
                            $result .= '    ' . $val . ',' . PHP_EOL;
                        }
                    } else {
                        $result .= '    ' . $key . ' => ' . $val . ',' . PHP_EOL;
                    }
                }
                $result .= '];' . PHP_EOL;
            }
        } else {
            $result .=  $value . ';' . PHP_EOL;
        }
        return $result;
    }

}