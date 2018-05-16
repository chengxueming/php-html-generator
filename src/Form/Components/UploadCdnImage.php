<?php
/**
 * @author cheng.xueming
 * @since  2018-05-16
 */

namespace cxm\htmlgen\Form\Components;


class UploadCdnImage extends BaseComponent {
    public function __construct($name , $c, $isShowPreview = false, $m="uploadImage", $value = "") {
        parent::__construct($name);
        $onclickJs =<<<JS
            public inputText = $($(this).closest("div").find("input[type=\'text\']")[0]);
            public inputFile = $($(this).closest("div").find("input[type=\'file\']")[0]);
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
