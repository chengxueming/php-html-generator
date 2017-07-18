<?php
require("../autoload.php");
function testDiv() {
	$a["successJump"] = ["android"=> "www.baidu.com", "ios"=>"www.google.com"];
	$a["successJump2"] = ["android"=> "www.baidu.com afsafsadgfsadf", "ios"=>"www.google.com"];
	$div = new Div("successJump");
	$div->addElem("安卓跳转", new Input("android", ""));
	$div->addElem("IOS跳转", new Input("ios", ""));
	$div->value = $a["successJump"];
	$div->value = $a["successJump2"];
	outputhtml("test", $div->innerHtml);
}

function outputhtml($file, $code) {
    $head =<<<EOF
        <meta charset="utf-8" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script type = "text/javascript" src="static/js/common.js"></script>
        <style type="text/css" src="common.css"></style>
EOF;
        #echo $head.$t->render();
        file_put_contents("./$file.html", $head.$code);
}

testDiv();