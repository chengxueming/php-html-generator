<?php
require("../autoload.php");
function testDivCondation() {
	$img = ["url"=>"www.baidu.com", "type"=>0, 
		"jump_url"=>[
			"android"=>"www.goole.com",
			"ios"=>"www.baidu.com",
		]
	];
	$div = new Div("");
	$div->addElem("CDN地址", new Input("url"));
	$div->addElem("类型", new Select("type", [0=>"图片", 1=>"跳转图片"]));
	$divjump = new Div("jump_url");
	$divjump->addElem("安卓跳转", new Input("android"));
	$divjump->addElem("IOS跳转", new Input("ios"));
	$div->addElem("跳转链接", $divjump, ["类型" => 1]);
	$div->value = $img;
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

function testClone() {
	$input = elem("input", ["id"=>"input12324", "name"=>"input1312"]);
	incrIdAndName($input);
	echo $input;
}

testDivCondation();
#testClone();
#echo date("Ymd H:i:s", time());