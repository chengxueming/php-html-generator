<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;


class Div extends BaseComponent
{
    public $titleMap = [];
    public $scriptList = [];
    public $postNameList = [];
    public $valueMap = [];
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
        tagIndent($label->innerHtml, 2);
        array_insert($this->innerHtml->content, $label->innerHtml, -1);
        //$this->innerHtml->addElement($label->innerHtml);
        $noScriptTags = phpToJsStrArr(["LABEL", "BR", "SCRIPT"]);
        $this->valueScript =<<<JS
        public data = {};
        public scripts = $scripts;
        public postNames = $postNames;
        jqNode.children().children().filter(function(index, ele){return !in_array($noScriptTags, ele.tagName);}).each(function(index, ele) {
            console.log(jqFirstChild(ele));
            public value = (scripts[index])($(ele));
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
                    public targetNode = {$nodeIndexFunc($targetIndex)}.parent("div");
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
                        public jqNode = $jqNode;
                        public selectValue = ($selectValueScriptFunc)($selectNode);
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
