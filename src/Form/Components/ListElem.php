<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;


class ListElem extends BaseComponent
{
    public $subElem = null;

    public function __construct($name, $subElem, $valueList = [])
    {
        parent::__construct($name);
        $subElem->innerHtml->addElement(elem("br"));
        $func = $subElem->valueScriptFunc;
        $this->subElem = $subElem;
        $this->innerHtml = elem("div", ["id"=>$this->id]);
        $this->value = $valueList;
        $this->valueScript =<<<JS
            public data = [];
            jqNode.children("div").each(function(index, ele) {
                public value = ($func)(jqFirstChild(ele));
                data.push(value);
            });
            return data;
JS;
    }

    public function setValue($valueList) {
        $subElem = $this->subElem;
        $mainId = $this->id;
        $addJs =<<<JS
        public outDiv = $(this).parent("div");
        public cloneLastChild = outDiv.children("div:last-child")[0].cloneNode(true);
        delCloneNodeId(cloneLastChild);
        $(cloneLastChild).find("input").each(function(index, ele) {
            $(ele).val("");
        });
        outDiv[0].appendChild(cloneLastChild);
JS;
        $delJs =<<<JS
        public outDiv = $(this).parent("div");
        if(outDiv.children("div").length == 1) {
            return ;
        }
        public lastChild = outDiv.children("div:last-child")[0];
        outDiv[0].removeChild(lastChild);
JS;
        $addBtnAttr = ["onclick"=>"$addJs;"];
        $delBtnAttr = ["onclick"=>"$delJs;"];
        $childElemList = [elem("button", $addBtnAttr, "添加"), elem("button", $delBtnAttr, "删除")];
        if(empty($valueList)) {
            $valueList = [""];
        }
        foreach($valueList as $v) {
            //修改html 内容
            $subElem->value = $v;
            //防止对象指向同一个问题
            tagIndent($subElem->innerHtml, 2);
            $childElemList[] = elem("div", [], $subElem->innerHtml->__toString());
            incrPropertys(["id", "name", "onchange", "onclick"], $subElem->innerHtml);
        }
        $this->innerHtml ->content = $childElemList;
    }
}
