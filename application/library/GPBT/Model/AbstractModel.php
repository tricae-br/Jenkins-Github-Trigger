<?php
namespace GPBT\Model;

abstract class AbstractModel
{
    public function setData(array $data = array())
    {
        $attributes = get_object_vars($this);
        $filteredData = array_intersect_key($data,$attributes);
        foreach($filteredData as $attribute => $value){
            $this->{$attribute} = $value;
        }
    }

    public function __get($attribute)
    {
        $attributes = get_object_vars($this);
        if(isset($attributes[$attribute])){
            return $this->$attribute;
        }
    }
}