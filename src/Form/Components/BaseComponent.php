<?php
namespace cxm\htmlgen\Form\Components;

class BaseComponent
{
    public $innerHtml = null;
    public $valueScriptFunc = null;
    public $postName = null;
    public $id = null;
    protected  $valueElem = null;
    private $value = null;
    private $valueScript = null;

    public function __construct($name)
    {
        $this->postName = $name;
        $class = get_class($this);
        $this->id = $class.rand(1000, 999999);
    }

    public function __set($property, $value)
    {

        if($property == "valueScript") {
            $value = preg_replace("/\"/", "'", $value);
            $id = $this->id;
            $func = $this->valueScriptFunc = <<<JS
            function(jqNode){ $value }
JS;
            $this->$property =<<<JS
             ($func)($("#$id"))
JS;
        }
        if($property == "value") {
            if(method_exists($this, "setValue")) {
                $this->setValue($value);
            }
            $this->value = $value;
        }
    }

    public function __get($property)
    {
        return $this->$property;
    }
}




#########################test###############################