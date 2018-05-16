<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;

class CheckBox extends BaseComponent
{
    public function __construct($name, $map, $selected = "")
    {
        parent::__construct($name);
        $id = $this->id;
        $div = elem("div", ["id"=>$id]);
        $this->valueElem = [];
        foreach($map as $k=>$v) {
            $attr = ["type"=>"checkbox", "value"=>$k];
            $inputElem = elem("input", $attr);
            $this->valueElem[] = $inputElem;
            $input= elem("div", ["style"=>"display:inline"], [$inputElem, elem("span", [], $v)]);
            $div->addElement(elem("label", [], $input));
        }
        $this->innerHtml = $div;
        //$this->value = $selected;
        $this->valueScript =<<<JS
        l = [];
        jqNode.find("input").each(function(index, ele){if(ele.checked){l.push(ele.value);}});
        return l.join(",");
JS;
    }
    public  function setValue($value)
    {
        $values = explode(",", $value);
        foreach($this->valueElem as $v) {
            if(isset($v["checked"])) {
                unset($v["checked"]);
            }
            if(in_array($v["value"], $values)) {
                $v["checked"] = "checked";
            }
        }
    }
}
