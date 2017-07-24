<?php
require("../autoload.php");
function testDivCondation() {
	$img = [
		"url"=>"a girl ride on a horse",
		"type"=>1, 
		"jump_url"=>[
			"praise"=>"a url to post a praise",
			"comment"=>"a url to post a comment",
		],
	];
	$div = new Div("");
	$div->addElem("内容", new Input("url"));
	$div->addElem("类型", new Select("type", [0=>"只读", 1=>"可以评论"]));
	$divjump = new Div("jump_url");
	$divjump->addElem("点赞", new Input("praise"));
	$divjump->addElem("评论", new Input("commit"));
	$div->addElem("功能", $divjump, ["类型" => 1]);
	$div->value = $img;
	outputhtml("test", $div->innerHtml);
}

function testListDivCondation() {
	$imgList = [
		[
		"url"=>"a boy ride on a horse",
		"type"=>0, 
		],
		[
		"url"=>"a girl ride on a horse",
		"type"=>1, 
		"jump_url"=>[
			"praise"=>"a url to post a praise",
			"comment"=>"a url to post a comment",
		],
		],
		[
		"url"=>"a boy ride on a horse",
		"type"=>0, 
		],
	];
	$div = new Div("");
	$div->addElem("内容", new Input("url"));
	$div->addElem("类型", new Select("type", [0=>"只读", 1=>"可以评论"]));
	$divjump = new Div("jump_url");
	$divjump->addElem("点赞", new Input("praise"));
	$divjump->addElem("评论", new Input("comment"));
	$div->addElem("功能", $divjump, ["类型" => 1]);
	$listElem = new ListElem("", $div, $imgList);
	outputhtml("test", $listElem->innerHtml);
}

function testComponents() {

	$t = new EditTable(["width"=>"70%", "align"=>"center" , "cellspacing"=>"0", "cellpadding"=>"6"]);
	$t->setData([
		"movie_name"=>"穆赫兰道",
		"origin_price"=>500,
		"sale_price"=>200,
		"type"=>"4,5"
		]);
    $t->row("电影名称", new Input("movie_name"));
    $t->row("票价", new Input("origin_price", "number", "$"));
    $t->row("售价", new Input("sale_price", "number", "$"));
    $t->row("类型", new CheckBox("type", [1=>"爱情", 2=>"恐怖", 3=>"童话", 4=>"推理", 5=>"悬疑"]));
    $t->submit("save", "index", [], "保存");
    outputhtml("test", $t->render());
}

function testListTable() {
	$t = new Table();
	$t->setData(
		[
			["movie_name"=>"穆赫兰道",
			"origin_price"=>500,
			"sale_price"=>200,
			"type"=>"4,5"
			],[
			"movie_name"=>"银河护卫队",
			"origin_price"=>300,
			"sale_price"=>100,
			"type"=>"4,5"
			]
		]
	);
	$t->column("movie_name", "商品ID");
	$t->column("origin_price", "票价($)");
	$t->column("sale_price", "原价($)");
	$t->column(function($row){return button("class", "method", [], "编辑");}, "编辑");
	outputhtml("test", $t->render());
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

function testIncrPropertys() {
	#$input = elem("input", ["id"=>"input12324", "name"=>"input1312", "onclick"=>'"#input12324"']);
	#incrPropertys(["id", "name"], $input);
	#var_dump($input->tag);
	$scr = elem("script", [], '"#input12324"');
	incrPropertys([], $scr);
	echo $scr;
}

function testInnerHtml() {
	$scr = elem("script", [], "hello");
	$scr ->innerHtml = "good apple";
	echo $scr;
}

testListTable();
#testClone();
#echo date("Ymd H:i:s", time());