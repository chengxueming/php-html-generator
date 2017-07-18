<?php
class BaseEdit {
    var $innerHtml = null;
    private $valueScript = null;
    var $valueScriptFunc = null;
    var $postName = null;
    var $id = null;
    private $value = null;
    protected  $valueElem = null;

    public function __construct($name) {
        $this->postName = $name;
        $class = get_class($this);
        $this->id = $class.rand(1000, 9999);
    }

    public function __set($property, $value) {

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

    public function __get($property) {
        return $this->$property;
    }
}

class Input extends BaseEdit {
    public function __construct($name, $value = "", $type="text", $propmt = "") {
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
    public  function setValue($value) {
        $this->valueElem["value"] = $value;
    }
}

class Label extends BaseEdit {
    var $sub = null;
    public function __construct($name ,$title, $sub) {
        parent::__construct($name);
        $id = $this->id;
        $this->innerHtml = elem("div", [], [elem("label", ["id"=>$id, "for"=>$sub->id], $title), $sub->innerHtml]);

        $func = $sub->valueScriptFunc;
        $this->sub = $sub;
        $this->valueScript =<<<JS
        return ($func)($(jqNode.children()[1]));
JS;
    }
}

class CheckBox extends BaseEdit {
    public function __construct($name, $map, $selected = "") {
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
    public  function setValue($value) {
        $values = explode(",", $value);
        foreach($this->valueElem as $v) {
            if(in_array($v["value"], $values)) {
                $v["checked"] = "checked";
            }
        }
    }
}

class Select extends BaseEdit {
    public function __construct($name, $map, $selected = "") {
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
        var selectNode = jqNode[0];
        var index = selectNode.selectedIndex;
        return selectNode.options[index].value;
JS;
    }

    public function setValue($value) {
        $values = explode(",", $value);
        foreach($this->valueElem as $v) {
            if(in_array($v["value"], $values)) {
                $v["selected"] = "selected";
            }
        }
    }
}


class ListElem extends BaseEdit {
    var $valueList = [];

    public function __construct($name, $subElem, $valueList = []) {
        parent::__construct($name);
        $this->valueList = $valueList;
        $mainId = $this->id;
        $addJs =<<<JS
        var cloneLastChild = $("#$mainId").children("div:last-child")[0].cloneNode(true);
        delCloneNodeId(cloneLastChild);
        $("#$mainId")[0].appendChild(cloneLastChild);
JS;
        $delJs =<<<JS
        if($("#$mainId").children("div").length == 1) {
            return ;
        }
        var lastChild = $("#$mainId").children("div:last-child")[0];
        $("#$mainId")[0].removeChild(lastChild);
JS;
        $addBtnAttr = ["onclick"=>"$addJs;"];
        $delBtnAttr = ["onclick"=>"$delJs;"];
        $childElemList = [elem("button", $addBtnAttr, "添加"), elem("button", $delBtnAttr, "删除")];
        if(empty($valueList)) {
            $valueList = [""];
        }
        foreach($valueList as $v) {
            //移除ID属性 维护自建ID
            //$html = preg_replace("/id=\"\w+\"/", "", $v ->innerHtml);
            $subElem->value = $v;
            $html = null;
            if(is_object($subElem->innerHtml)) {
                $html = $subElem->innerHtml->__toString();
            }else {
                $html = $subElem->innerHtml;
            }
            $childElemList[] = elem("div", [], $html);
        }
        $this->innerHtml = elem("div", ["id"=>$mainId], $childElemList);
        $func = $subElem->valueScriptFunc;
        $this->valueScript =<<<JS
            var data = [];
            jqNode.children("div").each(function(index, ele) {
                var value = ($func)(jqFirstChild(ele));
                data.push(value);
            });
            return data;
JS;
    }
}

class JoinElem extends BaseEdit {

}

class Div extends BaseEdit {
    var $titleMap = [];
    var $scriptList = [];
    var $postNameList = [];
    var $valueMap = [];

    public function __construct($name, $value = []) {
        parent::__construct($name);
        if(!empty($value)) {
            $this->value = $value;
        }
        $this->innerHtml = elem("div", ["id"=>$this->id]);
    }

    public function addElem($title, $elem, $dir = 0, $condation = []) {
        if(count($this->innerHtml->content) > 0 && $dir > 0) {
            $this->innerHtml->addElement(elem("br"));
        }
        $this->valueElem[$this->postName][$elem->postName] = $elem;
        if(isset($this->valueMap[$this->postName][$elem->postName])) {
            $elem->value = $this->valueMap[$this->postName][$elem->postName];
        }
        $this->scriptList[] = $elem->valueScriptFunc;
        $this->postNameList[] = $elem->postName;
        $scripts = "[".join(",", $this->scriptList)."]";
        $postNames = "['".join("','", $this->postNameList)."']";
        $label = new Label($elem->postName, $title, $elem);
        $this->innerHtml->addElement($label->innerHtml);
        $this->valueScript =<<<JS
        var data = {};
        var scripts = $scripts;
        var postNames = $postNames;
        jqNode.children().children().filter(function(index, ele){if(ele.tagName=="LABEL")return false;return true;}).each(function(index, ele) {
            console.log(jqFirstChild(ele));
            var value = (scripts[index])($(ele));
            data[postNames[index]] = value;
        });
        return data;
JS;
    }

    protected function setValue($value) {
        if(is_string($value)) {
            $value = [];
        }
        $this->valueMap = [$this->postName=>$value];
        if(empty($this->valueElem)) {
            return;
        }
        foreach($this->valueElem[$this->postName] as $postName => $valueElem) {
            if(isset($value[$postName])) {
                $valueElem->value = $value[$postName];
            }else {
                $valueElem->value = "";
            }
        }
    }
}

class uploadCdnImage extends BaseEdit {
    public function __construct($name , $c, $isShowPreview = false, $m="uploadImage", $value = "") {
        parent::__construct($name);
        $onclickJs =<<<JS
            var inputText = $($(this).closest("div").find("input[type=\'text\']")[0]);
            var inputFile = $($(this).closest("div").find("input[type=\'file\']")[0]);
            uploadImg(inputFile, inputText, "$c", "$m");
JS;
        $text_id = "input".rand(1000,9999);
        $file_id = "input".rand(1000,9999);
        $elems = [elem("input", ["type"=>"text", "id"=>$text_id, "name"=>$text_id]), elem("input", ["id"=>$file_id, "type"=>"file", "name"=>$file_id]), elem("button", ["onclick"=>$onclickJs], "上传")];
        $this->innerHtml = elem("div", ["id"=>$this->id], $elems);
        $this->value = $value;
        $this->valueScript =<<<JS
        return jqNode.find("input[type=\'text\']").val();
JS;
    }

    public function setValue($value) {
        $this->innerHtml->content[0]["value"] = $value;
    }
}

#########################test###############################