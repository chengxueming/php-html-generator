<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */
namespace cxm\htmlgen\Form\Components;

class Input extends BaseComponent
{

    public function __construct($name, $type="text", $propmt = "", $value = "")
    {
        parent::__construct($name);
        $id = $this->id;
        $propmt = empty($propmt)?"":"($propmt)";
        $attr = [];
        if($type == "time") {
            $attr =  ["onfocus"=>"WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})", "class"=>"Wdate"];
        }else{
            $attr = ["type"=>$type];
        }
        $this->valueElem = elem("input", $attr);
        $this->innerHtml = elem("div", ["id"=>$id], [$this->valueElem, elem("span", ["style"=>"color:red"], $propmt)]);
        $this->value = $value;
        $this->valueScript =<<<JS
        return jqNode.find("input").val();
JS;
    }

    public  function setValue($value)
    {
        $this->valueElem["value"] = $value;
    }
}