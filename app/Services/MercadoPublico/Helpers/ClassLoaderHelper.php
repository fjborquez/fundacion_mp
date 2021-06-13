<?php

namespace App\Services\MercadoPublico\Helpers;

class ClassLoaderHelper {
    public function loadClasses($classes, $namespace) {
        $classesArray = [];

        foreach ($classes as $class) {
            $className = $namespace . $class;

            if(class_exists($className)) {
                $classesArray[] = new $className;
            }
        }

        return $classesArray;
    }
}
