<?php
abstract class BaseEdit {
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
        $this->id = $class.rand(1000, 999999);
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
    public function __construct($name, $type="text", $value = "", $style = "middle") {
        parent::__construct($name);
        $id = $this->id;
        $propmt = empty($propmt)?"":"($propmt)";
        $attr = ["id"=>$id];
        if($type == "time") {
            $attr =  ["onfocus"=>"WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})", "class"=>"Wdate"];
        }else{
            $classes = ["small"=>["input-mini"], "middle"=>["input-medium"], "large"=>["input-xlarge"]];
            $classes = array_merge($classes[$style], []);
            $attr = ["type"=>$type, "class"=>$classes];
        }
        $this->valueElem = elem("input", $attr);
        $this->innerHtml = $this->valueElem;
        $this->value = $value;
        $this->valueScript =<<<JS
        return jqNode.find("input").val();
JS;
    }
    public  function setValue($value) {
        $this->valueElem["value"] = $value;
    }
}

class TextArea extends BaseEdit {
    public function __construct($name, $placeholder = "", $rows = 3) {
        parent::__construct($name);
        $this->valueElem = elem("textarea", ["rows"=>$rows, "placeholder"=>$placeholder, "class"=>["form-control", "input-xlarge"]]);
        $this->valueElem["id"] = $this->id;
        $this->innerHtml = $this->valueElem;
        $this->valueScript =<<<JS
        return jqNode.val();
JS;
    }
    public  function setValue($value) {
        $this->valueElem->innerHtml = $value;
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
    var $subElem = null;
    var $title = "";
    var $valueList = [];
    var $head = null;
    var $body = null;
    var $col_class = "";

    public function __construct($name, $subElem, $column = 3, $valueList = []) {
        parent::__construct($name);
        #$subElem->innerHtml->addElement(elem("br"));
        $func = $subElem->valueScriptFunc;
        $this->subElem = $subElem;
        $this->innerHtml = elem("div", ["id"=>$this->id, "style"=>"display:inline;"]);
        $this->head = elem("", []);
        $this->innerHtml->addElement($this->head);
        $this->setHead();
        $class = ["row"];
        $this->columns = $column;
        $this->col_class = "col-md-".intval(12/$column);
        if($subElem instanceof div) {
            $class = [];
            $this->col_class = "";
        }

        $this->body = elem("div", ["class"=>$class]);
        $this->innerHtml->addElement($this->body);
        $this->value = $valueList;

    }

    private function setHead() {
        $outDiv =<<<JS
        $($(this).parent("div").children("div")[0])
JS;
        $addJs =<<<JS
        var outDiv = {$outDiv};
        var cloneLastChild = outDiv.children("div:last-child")[0].cloneNode(true);
        delCloneNodeId(cloneLastChild);
        $(cloneLastChild).find("input").each(function(index, ele) {
            $(ele).val("");
        });
        outDiv[0].appendChild(cloneLastChild);
JS;
        $delJs =<<<JS
        var outDiv = {$outDiv};
        if(outDiv.children("div").length == 1) {
            return ;
        }
        var lastChild = outDiv.children("div:last-child")[0];
        outDiv[0].removeChild(lastChild);
JS;
        $addBtnAttr = ["onclick"=>"$addJs;", "class"=>["btn"], "type"=>"button"];
        $delBtnAttr = ["onclick"=>"$delJs;", "class"=>["btn"], "type"=>"button"];
        $func = $this->subElem->valueScriptFunc;
        $this->valueScript =<<<JS
        var data = [];
        jqNode.children("div").each(function(index, ele) {
            var value = ($func)(jqFirstChild(ele));
            data.push(value);
        });
        return data;
JS;
        $childElemList = [elem("button", $addBtnAttr, "添加{$this->title}"), elem("", [], "&nbsp;"), elem("button", $delBtnAttr, "删除{$this->title}")];
        $this->head->content = $childElemList;
    }

    public function setTitle($title) {
        $this->title = $title;
        if(method_exists($this->subElem, "setTitle")) {
            $this->subElem->setTitle($title);
        }else {
            $this->subElem->innerHtml = elem("", [], [elem("label", [], "{$title}："), $this->subElem->innerHtml]);
        }
        //重置 innerhtml
        $this->setHead();
        $this->setValue($this->valueList);
    }

    public function setValue($valueList) {
        $this->valueList = $valueList;
        $subElem = $this->subElem;
        $childElemList = [];
        if(empty($valueList)) {
            $valueList = [""];
        }
        foreach($valueList as $v) {
            //修改html 内容
            $subElem->value = $v;
            //防止对象指向同一个问题
            //tagIndent($subElem->innerHtml, 2);
            $childElemList[] = elem("div", ["class"=>[$this->col_class]], $subElem->innerHtml->__toString());
            incrPropertys(["id", "name", "onchange", "onclick"], $subElem->innerHtml);
        }
        $this->body->content = $childElemList;
    }
}

class BaseCondation {
    public function __construct() {

    }
}

abstract class Div extends BaseEdit {
    var $titleMap = [];
    var $scriptList = [];
    var $postNameList = [];
    var $valueMap = [];
    private $bodyScripts = [];

    public function __construct($name, $tag) {
        parent::__construct($name);
        $this->innerHtml = elem($tag, ["id"=>$this->id], ["", elem("script")]);
    }

    //通过一个base edit获得处理后的html 一般是加标题
    abstract protected function addTitle($html, $title);

    public function setTitle($title) {}

    protected function get_html($elem, $title) {
        $html = $this->addTitle($elem->innerHtml, $title);
        //List要特殊处理 标题要在每个list子元素加 而不是 在按钮之前
        if(method_exists($elem, "setTitle") && get_class($this) != "Nav") {
            $elem->setTitle($title);
            $html = addTitle($elem->innerHtml, "", 2);
        }
        return $html;
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
        $html = $this->get_html($elem, $title);
        array_insert($this->innerHtml->content, $html, -1);
        $noScriptTags = phpToJsStrArr(["LABEL", "BR", "SCRIPT", "BUTTON"]);
        $this->valueScript =<<<JS
        var data = {};
        var scripts = $scripts;
        var postNames = $postNames;
        jqNode.children().filter(function(index, ele){return !in_array($noScriptTags, ele.tagName);}).each(function(index, ele) {
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
               $($jqNode.children()[$index])
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

class Block extends Div {
    var $title = null;

    public function __construct($name, $title, $color = "alert-success", $value = []) {
        parent::__construct($name, "div");
        $this->setTitle($title);
        $this->innerHtml["class"] = ["block", "alert", $color, "form-inline"];
    }

    //公用addTitle 前后加上空格和冒号
    protected function addTitle($html, $title) {
       return addTitle($html, $title);
    }

    public function setTitle($title) {
        if(!empty($this->title)) {
            array_shift($this->innerHtml->content);
        }
        if(!empty($title)) {
            array_unshift($this->innerHtml->content, elem("", [], "&nbsp;<label>{$title}</label>&nbsp;：<br>&nbsp;&nbsp;&nbsp;"));
            $this->title = $title;
        }
    }
}                                                                                                                                                                          

class Form extends Div {
    var $title = null;
    var $head = "";
    var $tail = "";

    public function __construct($name, $style = "form-inline") {
        parent::__construct($name, "div");
        $this->innerHtml["class"] = ["form-inline"];
    }


    private function addFormControl($html) {
        $control_tags = ["INPUT", "TEXTAREA"];
        if(in_array($html->tagName, $control_tags)) {
            if(!$html->hasClass("form-control"))
                $html->addClass("form-control");
        }
        foreach($html->content as $v)
            $this->addFormControl($v);
    }

    //套上form-group 并且添加title label
    protected function addTitle($html, $title) {
        $this->head = "&nbsp;&nbsp";
        #$html->addClass("form-control");
        $this->addFormControl($html);
        $html = elem("", [], [elem("div", ["class"=>["form-group"]], [elem("", [], $this->head), elem("label", [], "{$title}："), $html]), $this->tail]);
        return $html;
    }

    public function addElemBr() {
        $this->tail = "<br>";
        $arguments = func_get_args();
        call_user_func_array(array($this, "addElem"), $arguments);
        $this->tail = "";
    }
}

class Nav extends Div {
    var $nav = null;
    var $tab_content = null;

    public function __construct($name) {
        parent::__construct($name, "div");
        $this->innerHtml["class"] = ["well"];
        $this->nav = elem("ul", ["class"=>["nav", "nav-tabs"]]);
        $this->tab_content = elem("div", ["class"=>["tab-content"]]);
        $this->innerHtml->addElement($this->nav);
        $this->innerHtml->addElement($this->tab_content);
        $this->innerHtml = $this->tab_content;
    }

    protected function addTitle($html, $title) {
        $id = get_new_gen_attr($this->id, count($this->tab_content->content) + 1);
        $li_classes = [];
        if(empty($this->tab_content->content)) {
            $li_classes[] = "active";
        }
        $this->nav->addElement(elem("li", ["class"=>$li_classes], elem("a", ["href"=>"#{$id}", "data-toggle"=>"tab"], $title)));
        $classes = ["tab-pane"];
        if(empty($this->tab_content->content)) {
            $classes = array_merge($classes, ["active", "in"]);
        }
        $html = elem("div", ["id"=>$id, "class"=>$classes], $html);
        return $html;
    }

    public function addTab() {

        $arguments = func_get_args();
        call_user_func_array(array($this, "addElem"), $arguments);
    }
}

class UploadCdnImage extends BaseEdit {
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
        $this->innerHtml = elem("div", ["id"=>$this->id, "class"=>"input-medium", "style"=>"display:inline"], $elems);
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