<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;


class Select extends BaseComponent
{
    public function __construct($name, $map, $selected = "")
    {
        parent::__construct($name);
        $id = $this->id;
        $div = elem("select", ["id"=>$id]);
        foreach($map as $k=>$v) {
            $attr = ["value"=>$k];
            $option = elem("option", $attr, $v);
            $this->valueElem[] = $option;
            $div->addElement($option);
        }
        $this->innerHtml = $div;
        $this->value = $selected;
        $this->valueScript =<<<JS
        public selectNode = jqNode[0];
        public index = selectNode.selectedIndex;
        return selectNode.options[index].value;
JS;
    }

    public function setValue($value)
    {
        $values = explode(",", $value);
        foreach($this->valueElem as $v) {
            if(isset($v["selected"])) {
                unset($v["selected"]);
            }
            if(in_array($v["value"], $values)) {
                $v["selected"] = "selected";
            }
        }
    }
}