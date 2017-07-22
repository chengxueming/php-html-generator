<?php
class EditTable {
    var $t = null;
    var $tHead = null;
    var $titleRow = null;
    var $tBody = null;
    var $valueScript = [];
    var $bodyScript = [];
    var $idMap = [];
    var $data = array();
    var $c = null;
    var $m = null;
    var $titleMap = [];

    public function __construct($attr = []) {
        $this->titleRow = elem("tr");
        $this->tHead = elem("thead", [], $this->titleRow);
        $this->tBody = elem("tbody");
        $this->t = elem("table", $attr, [$this->tHead, $this->tBody]);
        global $c;
        global $m;
        $this->c = isset($c)?$c:"pc_prize";
        $this->m = isset($m)?$m:"edit";
    }

    public function insertHeadRow() {

    }

    public function insertBodyRow() {

    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getTHead() {
        return $this->tHead;
    }

    public function getTBody() {
        return $this->tBody;
    }

    public function column($value, $title = null, $attrTitle = [], $attrBody = []) {
        if(null !== $title){
            safeSetArr($attrTitle, ["width"=>"%"]);
            $this->titleRow->addElement(elem("th", $attrTitle, $title));
        }
        $tBodyRows = $this->tBody->content;
        reset($tBodyRows);
        foreach($this->data as $rowNum => $row) {
            if(is_callable($value)) {
                $innerHtml = $value($row);
            } elseif(is_string($value)) {
                $innerHtml = $row[$value];
            }

            $cur_attr = $attrBody;
            if(is_numeric($innerHtml)) {
                safeSetArr($cur_attr, ["class"=>[]]);
                $cur_attr["class"][] = "num2";
            }
            current($tBodyRows) -> addElement(elem("td", $cur_attr, $innerHtml));
            next($tBodyRows);
        }
    }

    public function setEditData($row) {
        $this->data = $row;
    }

    private function setDeepProperty($ele, $property) {
        if(is_string($ele) || is_array($ele)) {
            return;
        }
        foreach($property as $k => $v) {
            $ele[$k] = $v;
        }
        if(count($ele->content) > 0) {
            foreach($ele->content as &$v) {
                $this->setDeepProperty($v, $property);
            }
        }
    }
    
    /**
    *@param $title type str 
    *@param $value 
    */
    public function row($title, $value, $readonly=false, $condation = []) {
        $tRow = elem("tr", [] ,elem("td", [], $title));
        $this->tBody->addElement($tRow);
        //可以作为子元素的
        if(is_array($value) || is_string($value) || (get_class($value) == "HtmlGenerator\HtmlTag")) {
            $values = $value;
            if($readonly && !is_string($value)) {
                if(!is_array($value)) {
                    $values = [$value];
                }
                foreach($values as &$v) {
                    $v["readonly"] = true;
                }
            }
            $tRow->addElement(elem("td", [], $values));
        //作为自定义类的
        }else {
            $judge_value = $value->value;
            if(empty($judge_value)) {
                 $value->value = isset($this->data[$value->postName])?$this->data[$value->postName]:"";
            }
            if($readonly) {
                $this->setDeepProperty($value->innerHtml, ["readonly"=>"readonly"]);
            }

            $tRow->addElement(elem("td", [], $value->innerHtml));
            $this->valueScript[$value->postName] = [$value->valueScript];
            $this->titleMap[$title] = $value->postName;
            $this->idMap[$value->postName] = $value->id;
        }

        if(!empty($condation)) {
            foreach($condation as $rowName => $rowValue) {
                $script = $this->valueScript[$this->titleMap[$rowName]][0];
                $id = $this->idMap[$this->titleMap[$rowName]];
                $jsArr = phpToJsStrArr($rowValue);
                $targetId = $value->id;
                $this->bodyScript[] =<<<JS
                $id$targetId = function(){
                    if($jsArr.indexOf($script) !== -1) {
                        $("#$targetId").closest("tr").show();
                    } else {
                        $("#$targetId").closest("tr").hide();
                    }
                };
                $id$targetId();
                $("#$id")[0].addEventListener("change", $id$targetId);
JS;
            }
        }
    }

    public function submit($saveMethod, $listMethodName, $args, $text="保存") {
        $params = $this->valueScript;
        $params["ajax"] = ["type"=>"POST"];
        $btn = elem("button", ["id"=>"submitbutton"], $text);
        $tRow = elem("tr", [] ,elem("td", [], $btn));
        $this->tBody->addElement($tRow);

        $successJs = "window.location='".ci_link($this->c, $listMethodName, [], "&")."';";
        $args = array_merge($args, ["c"=>$this->c, "m"=>$saveMethod]);
        $click_js = ajax($params, $successJs, $args, "&");
        $this->bodyScript[] =<<<JS
        $("#submitbutton")[0].addEventListener("click", function(){ $click_js });
JS;
    }

    public function render() {
        return $this->t.elem("script", ["type"=>"text/javascript"], join(";", $this->bodyScript));
    }

}
?>