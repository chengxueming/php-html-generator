<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;


class Label extends BaseComponent
{
    public $sub = null;
    public function __construct($name ,$title, $sub) {
        parent::__construct($name);
        $id = $this->id;
        $sub->innerHtml["style"] = "display:inline";
        $this->innerHtml = elem("div", [], [elem("label", ["id"=>$id, "for"=>$sub->id], "$title:"), $sub->innerHtml]);

        $func = $sub->valueScriptFunc;
        $this->sub = $sub;
        $this->valueScript =<<<JS
        return ($func)($(jqNode.children()[1]));
JS;
    }
}