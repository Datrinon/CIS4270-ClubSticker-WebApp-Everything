<?php

/**
 * Represents product in the GuitarShop application
 *
 * @author jam
 * @version 180706
 */
class Product {
    
    private $id; // You should never touch this -- id 
    private $productCode;
    private $name;
    private $listPrice;
    private $uploader;
 
    /**
     * Builds an object with instance variables set. Only the instance variables
     * will be set that correspond to the input data (i.e., not all instance
     * variables will be set in all cases.
     * @param array $data Optional values to be loaded in instance variables.
     */
    function __construct($data = array()) {
        if (!is_array($data)) {
            trigger_error('Non-array input to ' . get_class() . 'constructor');
        } else 
        
        // If the input array has at least one value, set the corresponding
        // instance variable.
        if ($data !== null && $data > 0) {
            foreach ($data as $name => $value) {
                $this->$name = $value;  
            }
        }
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
