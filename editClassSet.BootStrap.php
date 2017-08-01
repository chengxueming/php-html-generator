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

abstract class BaseInput extends BaseEdit {
    public function __construct($name, $type="text", $placeholder = "", $tagName, $withToolBar = true) {
        parent::__construct($name);
        $attr = ["type"=>$type, "placeholder"=>$placeholder];
        $toolBarJs =<<<JS
        $('#page').html($(this).val());
        var input = $(this);
        $( '#edit-toolbar' ).dialog(
            {  modal: true,
               autoOpen: false,
               close:function() {      input.val($('#page').html());    }    
            }); 
        $('#edit-toolbar').dialog('open');
JS;
        $this->valueElem = elem($tagName, $attr);
        $this->valueElem["id"] = $this->id;
        if($withToolBar) {
            $this->valueElem["onclick"] = $toolBarJs;
        }   
        $this->innerHtml = $this->valueElem;
        $this->valueScript =<<<JS
        return jqNode.closest('$tagName').val();
JS;
    }
    public  function setValue($value) {
        $this->valueElem["value"] = $value;
    }
}

class Input extends BaseInput {
    public function __construct($name, $type="text", $placeholder = "", $style = "middle") {
        parent::__construct($name, $type, $placeholder, "input");
        if($type == "time") {
            $this->valueElem["onfocus"] = "WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd HH:mm:ss'})";
            $this->valueElem->addClass("Wdate");
        }else{
            $classes = ["small"=>"input-mini", "middle"=>"input-medium", "large"=>"input-xlarge"];
            $classe = $classes[$style];
            $this->valueElem->addClass($classe);
        }
    }
}

class TextArea extends BaseInput {
    public function __construct($name, $placeholder = "", $rows = 3) {
        parent::__construct($name, "text", $placeholder, "textarea");
        $this->valueElem["rows"] = $rows;
        $this->valueElem->addClass("form-control");
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
    var $subElemMap = null;
    var $title = "";
    var $valueList = [];
    var $head = null;
    var $body = null;
    //每个外层div的样式
    var $out_class = "";
    //每个subElem的样式
    var $ele_class = "";
    var $tool_bar = null;

    public function __construct($name, $subElemMap, $column = 3) {
        parent::__construct($name);
        $this->innerHtml = elem("div", ["id"=>$this->id, "style"=>"display:inline;"]);
        $this->head = elem("div", ["type"=>"tool-bar", "class"=>["input-group"]]);
        $this->innerHtml->addElement($this->head);
        $this->subElemMap = $subElemMap;
        $this->columns = $column;
        //列表div的样式
        $class = [];
        if(is_array($subElemMap) || $subElemMap instanceof Div) {
            $class = [];
            //如果是map 或 div后面有+-toolbar
            $this->out_class = "row";
            $this->ele_class = "col-md-8";
        }else {
            $class[] = "row";
            // input textarea 等没有 但
            $this->out_class = "col-md-".intval(12/$column);
            $this->ele_class = "";
        }
        if(!is_array($subElemMap)) {
            $this->subElem = $subElemMap;
            $this->subElemMap = ["eye"=>$subElemMap];
        }
        $this->setHead();
        $this->body = elem("div", ["class"=>$class, "type"=>"content"]);
        $this->innerHtml->addElement($this->body);
        $this->value = [];
    }

    private function addJsFunc($toolBarJs, $insertJs) {
        $selectFunc = 'select_func';
        $jsHtmlMap = phpToJsMap($this->subElemMap, "innerHtml");
        return<<<JS
        var toolBar = $toolBarJs;
        var title = {$selectFunc('toolBar.children(\'select\')')} || 'eye';
        var html = {$jsHtmlMap}[title];
        $(html).attr('tilte', title);
        delCloneNodeId(html);
        $insertJs;
JS;
    }

    private function setHead() {
        $clear_tag = phpToJsStrArr(["input", "textarea"]);
        $list_content = function($main){return "$main.children('div[type=\'content\']')";};
        $funcs = phpToJsMap($this->subElemMap, "valueScriptFunc", true);
        $this->valueScript =<<<JS
        var data = [];
        {$list_content('jqNode')}.children("div").each(function(index, ele) {
            var title = $(ele).attr("title");
            console.log(ele);
            console.log("function is:", {$funcs});
            console.log("tilte i get is:", title);
            var value = ({$funcs}[title])(jqChild(ele, 1));
            data.push(value);
        });
        return data;
JS;
        $this->tool_bar = $this->tool_bar_func();
        $this->head->content = $this->head_bar_elems_func($list_content);
    }

    protected function head_bar_elems_func($list_content) {
        $outDiv =<<<JS
        $($(this).parent("div").parent("div")[0])
JS;
        $toolBarHtml = $this->tool_bar_func()->__toString();
        $insertJs =<<<JS
        var toolBar = $("$toolBarHtml");
        // html = toolBar.insertBefore($(html));
        // console.log(html);
        var div = $('<div class=\'{$this->out_class}\'></div>').append($(html));
        {$list_content($outDiv)}.append(div);
JS;
        $addJs = $this->addJsFunc("{$outDiv}.children('div[type=\'tool-bar\']')", $insertJs);
        $addBtnAttr = ["onclick"=>"$addJs;", "class"=>["btn"], "type"=>"button"];
        $select_html = $this->get_select_html();
        $addButton = elem("button", $addBtnAttr, "添加{$this->title}");
        $addButton = addSibling($select_html, $addButton);
        $delButton = elem("");
        if(!is_null($this->subElem)) {
            $delJs =<<<JS
            var content_div = {$list_content($outDiv)};
            var lastChild = content_div.children("div:last-child")[0];
            $(lastChild).remove();
JS;
            $delBtnAttr = ["onclick"=>"$delJs;", "class"=>["btn"], "type"=>"button"];
            $delButton = elem("button", $delBtnAttr, "删除{$this->title}");
        }
        return [$addButton, elem("", [], "&nbsp;"), $delButton];
    }

    protected function get_select_html() {
        if(!is_null($this->subElem)) {
            return elem("");
        }
        $select = new Select("", array_keys($this->subElemMap));
        $select->innerHtml["style"] = "width: auto;";
        $select->innerHtml->addClass("form-control");
        return $select->innerHtml;
    }

    protected function tool_bar_func() {
        if(!is_null($this->subElem)) {
            return elem("");
        }
        $select_html = $this->get_select_html();
        $span = elem("span", ["aria-hidden"=>"true", "class"=>["glyphicon"]]);
        $del_span = clone $span;
        $span->addClass("glyphicon-plus");
        $del_span->addClass("glyphicon-minus");
        $button_attr = ["type"=>"button", "class"=>["btn", "btn-default"]];
        $toolBarJs = "$(this).parent('div')";
        $listElemJs = "$toolBarJs.parent('div')";
        $insertJs = <<<JS
            var div = $('<div class=\'{$this->out_class}\'></div>').append($(html).addClass('{$this->ele_class}').prop('outerHTML'));
            var div = div.append($toolBarJs.prop('outerHTML'));
            div.insertAfter($listElemJs);
JS;
        $add_attr = array_merge($button_attr, ["onclick"=>$this->addJsFunc($toolBarJs,  $insertJs)]);
        $delJs = <<<JS
        $listElemJs.remove();
JS;
        $del_attr = array_merge($button_attr, ["onclick"=>$delJs]);
        $add_button = elem("button", $add_attr, [$span]);
        $del_button = elem("button", $del_attr, [$del_span]);
        return elem("div", ["class"=>["input-group", "col-md-4"]], [$select_html, $add_button, $del_button]);
    }

    public function setTitle($title) {
        if(is_null($this->subElem)) {
            return false;
        }
        $this->title = $title;
        if(method_exists($this->subElem, "setTitle")) {
            $this->subElem->setTitle($title);
        }else {
            $this->subElem->innerHtml = elem("", [], [elem("label", [], "{$title}："), $this->subElem->innerHtml]);
        }
        //重置 innerhtml
        $this->setHead();
        $this->setValue($this->valueList);
        return true;
    }

    public function setValue($valueList) {
        print_r($valueList);
        $this->valueList = $valueList;
        $childElemList = [];
        if(empty($valueList) && is_null($this->subElem)) {
            $valueList = [];
            $valueList[] = ["", array_keys($this->subElemMap)[0]];
        }
        foreach($valueList as $value) {
            //修改html 内容
            $v = $value;
            var_dump($v);
            $subElem = $this->subElem;
            $title = "eye";
            if(is_null($this->subElem)) {
                $v = $value[0];
                $title = $value[1];
                $subElem = $this->subElemMap[$title];
            }
            //防止对象指向同一个问题
            //tagIndent($subElem->innerHtml, 2);
            $subElem->value = $v;
            $subElem->innerHtml->addClass($this->ele_class);
            $html = elem("", [], [$subElem->innerHtml->__toString(), $this->tool_bar]);
            $childElemList[] = elem("div", ["class"=>[$this->out_class], "title"=>$title], $html);
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
    protected $eachElemJs = "jqNode.children()";
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
        var eleDir = [].slice.call({$this->eachElemJs}).filter(function(ele, index){return !in_array($noScriptTags, ele.tagName);});
        console.log(scripts);
        eleDir.forEach(function(ele, index) {
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
        $this->eachElemJs =<<<JS
        (function(jqNode) {
        return [].slice.call(jqNode.children()).reduce(function(cart, ele) {
            if ($(ele).attr("type") == "single") {
             cart.push($(ele).children()[1]);
             }
            else cart.push(ele);
            return cart;
        }, []);})(jqNode)
JS;
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
        $html = elem("", [], [elem("div", ["class"=>["form-group"], "type"=>"single"], [elem("", [], $this->head), elem("label", [], "{$title}："), $html]), $this->tail]);
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
        $this->eachElemJs =<<<JS
        (function(jqNode) {
            var cart = [].slice.call(jqNode.children("div[class=tab-content]").children()).reduce(function(cart, ele){
                cart.push($(ele).children()[0]);
                return cart;
            }, []);
            console.log(cart);
            return cart.reverse();
        })(jqNode)
JS;
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