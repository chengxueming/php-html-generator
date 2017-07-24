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
    public function __construct($name, $type="text", $propmt = "", $value = "") {
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
        $sub->innerHtml["style"] = "display:inline";
        $this->innerHtml = elem("div", [], [elem("label", ["id"=>$id, "for"=>$sub->id], "$title:"), $sub->innerHtml]);

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
            if(isset($v["checked"])) {
                unset($v["checked"]);
            }
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
            if(isset($v["selected"])) {
                unset($v["selected"]);
            }
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
        $subElem->innerHtml->addElem(elem("br"));
        $this->valueList = $valueList;
        $mainId = $this->id;
        $addJs =<<<JS
        var cloneLastChild = $("#$mainId").children("div:last-child")[0].cloneNode(true);
        delCloneNodeId(cloneLastChild);
        $(cloneLastChild).find("input").each(function(index, ele) {
            $(ele).val("");
        });
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
            //修改html 内容
            $subElem->value = $v;
            //防止对象指向同一个问题
            $childElemList[] = elem("div", [], $subElem->innerHtml->__toString());
            incrPropertys(["id", "name", "onchange"], $subElem->innerHtml);
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


class Div extends BaseEdit {
    var $titleMap = [];
    var $scriptList = [];
    var $postNameList = [];
    var $valueMap = [];
    private $bodyScripts = [];

    public function __construct($name, $value = []) {
        parent::__construct($name);
        if(!empty($value)) {
            $this->value = $value;
        }
        $this->innerHtml = elem("div", ["id"=>$this->id], elem("script"));
    }

    public function addElem($title, $elem, $condation = []) {
        $this->valueElem[$this->postName][$elem->postName] = $elem;
        if(isset($this->valueMap[$this->postName][$elem->postName])) {
            $elem->value = $this->valueMap[$this->postName][$elem->postName];
        }
        $this->scriptList[$elem->postName] = $elem->valueScriptFunc;
        $this->postNameList[$elem->postName] = $elem->postName;
        $scripts = "[".join(",", $this->scriptList)."]";
        $postNames = "['".join("','", $this->postNameList)."']";
        $label = new Label($elem->postName, $title, $elem);
        array_insert($this->innerHtml->content, $label->innerHtml, -1);
        //$this->innerHtml->addElement($label->innerHtml);
        $noScriptTags = phpToJsStrArr(["LABEL", "BR", "SCRIPT"]);
        $this->valueScript =<<<JS
        var data = {};
        var scripts = $scripts;
        var postNames = $postNames;
        jqNode.children().children().filter(function(index, ele){return !in_array($noScriptTags, ele.tagName);}).each(function(index, ele) {
            console.log(jqFirstChild(ele));
            var value = (scripts[index])($(ele));
            data[postNames[index]] = value;
        });
        return data;
JS;

        $this->titleMap[$title] = $elem->postName;
        if(!empty($condation)) {
            foreach($condation as $titleSelect => $valueSelect) {
                //修改select节点的onchange用于动态加载 1.获取自身的值 2.根据值判断是否toggle
                $postName = $this->titleMap[$titleSelect];
                $selectValueScriptFunc = $this->scriptList[$postName];
                $selectValueScriptFunc = $this->scriptList[$postName];
                $selectNode = $this->valueElem[$this->postName][$postName]->innerHtml;
                $selectIndex = array_search($postName, array_keys($this->scriptList));
                $targetIndex = count($this->scriptList) - 1;
                $phpToJsStrArr = "phpToJsStrArr";
                $nodeIndexFunc = function($index, $jqNode = "jqNode"){
                return <<<JS
                $($($jqNode.children()[$index]).children()[1])
JS;
                };
                $script = <<<JS
                    var targetNode = {$nodeIndexFunc($targetIndex)}.parent("div");
                    if({$phpToJsStrArr($valueSelect)}.indexOf(selectValue) != -1) {
                        targetNode.show();
                    } else {
                        targetNode.hide();
                    }
JS;
                $selectFunc = function($source, $selectNode, $jqNode) use($script, $selectValueScriptFunc) {
                    $scripts = [];
                    if(!empty($source)) {
                        $scripts = explode(";", $source);
                    }else{
                        $scripts []=<<<JS
                        var jqNode = $jqNode;
                        var selectValue = ($selectValueScriptFunc)($selectNode);
JS;
                    }
                    $scripts[] = "$script";
                    return join(";", $scripts);
                };
                $selectNode["onchange"] = $selectFunc($selectNode["onchange"], '$(this)', '$(this).parent("div").parent("div")');
                //初始化标签节点中的select 页面加载时的condation情况
                $this->bodyScripts = $selectFunc($this->bodyScripts, $nodeIndexFunc($selectIndex), "$(\"#{$this->id}\")");
                //修改内置script标签 1.获取select的值 2.根据值判断是否toggle
                $this->innerHtml->content[count($this->innerHtml->content) - 1] = elem("script", ["type"=>"text/javascript"], $this->bodyScripts);
            }
        }

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