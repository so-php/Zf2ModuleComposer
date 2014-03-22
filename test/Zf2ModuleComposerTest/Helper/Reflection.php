<?php


namespace Zf2ModuleComposerTest\Helper;


use ReflectionClass;

class Reflection {
    /**
     * @param mixed $object
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function invokeArgs($object, $method, array $args = array()){
        $rc = new ReflectionClass($object);
        $method = $rc->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }
} 