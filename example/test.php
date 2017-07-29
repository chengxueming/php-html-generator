<?php
require("../autoload.php");
function testDivCondation() {
	$img = [
	[
		"url"=>"a girl ride on a horse",
		"type"=>1, 
		"jump_url"=>[
			"praise"=>"a url to post a praise",
			"comment"=>"a url to post a comment",
			"name" => [
				"frst"=>"jhon",
				"last"=>"ham",
				"type"=>1,
				"addion"=>[
					"before"=>12,
					"after"=>13,
				]
			],
			"more"=>[
				"ansewer1",
				"ansewer2",
				"ansewer3",
				"ansewer4",
				"ansewer5",
			]
		],
	],	
	[
		"url"=>"a girl ride on a horse",
		"type"=>1, 
		"jump_url"=>[
			"praise"=>"a url to post a praise",
			"comment"=>"a url to post a comment",
			"name" => [
				"frst"=>"jhon",
				"last"=>"ham",
				"type"=>1,
				"addion"=>[
					"before"=>12,
					"after"=>13,
				]
			],
			"more"=>[
				"ansewer1",
				"ansewer2",
				"ansewer3",
				"ansewer4",
				"ansewer5",
			]
		],
	],
	];
	$div = new Div("");
	$div->addElem("内容", new Input("url"));
	$div->addElem("类型", new Select("type", [0=>"只读", 1=>"可以评论"]));
	$divjump = new Div("jump_url");
	$divjump->addElem("点赞", new Input("praise"));
	$divjump->addElem("评论", new Input("comment"));
		$divname = new Div("name");
	$divname->addElem("FIRST_NAME", new Input("frst"));
	$divname->addElem("LAST_NAME", new Input("last"));
	$div->addElem("功能", $divjump, ["类型" => 1]);
	$divjump->addElem("name-part", $divname);
	$divaddion = new Div("addion");
	$divaddion->addElem("BEFORE", new Input("before"));
	$divaddion->addElem("AFTER", new Input("after"));
	$divname->addElem("TYPE", new Select("type", [0=>"normal", 1=>"not"]));
	$divname->addElem("ADDTION", $divaddion, ["TYPE"=>1]);
	$divjump->addElem("MORE", new ListElem("more", new Input("")));
	$le = new ListElem("", $div);
	$le->value = $img;
	outputhtml("test", $le->innerHtml, "normal");
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
	$listElem = new ListElem("", $div);
	$listElem ->value = $imgList;
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

function outputhtml($file, $code, $type = "boot") {
    $boot_head =<<<EOF
        <meta charset="utf-8" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type = "text/javascript" src="static/js/common.js"></script>
        <style type="text/css" src="common.css"></style>
EOF;
    $normal_head =<<<EOF
        <meta charset="utf-8" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
        <script type = "text/javascript" src="static/js/common.js"></script>
        <style type="text/css" src="common.css"></style>
EOF;
        #echo $head.$t->render();
        if($type == "normal") {
        	$boot_head = $normal_head;
        }
        file_put_contents("./$file.html", $boot_head.$code);
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

function testListDivList() {
	$div = new Div("div");
	$init = [
	 ["www.baidu.com", "www.baidu.com", "www.baidu.com", "www.baidu.com"],
	 ["www.baidu.com", "www.baidu.com", "www.baidu.com", "www.baidu.com"],
	]; 
	$le = new ListElem("a", new Input("", "text"));
	$outlist = new ListElem("outlist", $le);
	$outlist->value = $init;
	outputhtml("test", $outlist->innerHtml);
}


function tsetExam() {
	$json = file_get_contents("./listening.txt");
	$arr = json_decode($json, true);
	$part = new Div("");
	$part->addElem("类型", new Input("type", "text"));
	$part->addElem("头部", new Input("head", "text"));
	function getSection() {
		function getPassage() {
			$sectionPassage = new Div("");
			$sectionPassage->addElem("名称", new Input("passage_name"));
			$sectionPassage->addElem("引导名称", new Input("passage_directins"));
			$sectionPassage->addElem("图片", new UploadCdnImage("image", "pc_prize"));
			$le = new ListElem("passage", $sectionPassage);
			$passageContent = new Div("content");
			$passageContent->addElem("内容类型", new Input("content_type"));
			$sectionPassage->addElem("content", $passageContent);
			$questionBody = new Div("");
			$questionBody->addElem("序号", new Input("number", "number"));
			$questionBody->addElem("问题内容", new Input("question_body ", "number"));
			$questionBody->addElem("问题名称", new Input("answer_name", "number"));
			$answer = new ListElem("answer", new Input(""));
			$questionBody->addElem("答案列表", $answer);
			$question = new ListElem("", $questionBody);
			$sectionPassage->addElem("问题", $question);
			return $le;
		}
		$sectionBody = new Div("");
		$sectionBody->addElem("名称", new Input("name"));
		$sectionBody->addElem("引导名称", new Input("name_directions"));
		$sectionBody->addElem("引导内容", new Input("directions"));
		$sectionBody->addElem("主干", getPassage());
		return $sectionBody;
	}
	$section = new ListElem("section", getSection());
	$part->addElem("节", $section);
	$part->value = $arr;
	outputhtml("test", $part->innerHtml);
}

function testPassage() {
	$json = file_get_contents("./listening.txt");
	$arr = json_decode($json, true);
	function getPassage() {
		$sectionPassage = new Div("");
		$sectionPassage->addElem("名称", new Input("passage_name"));
		$sectionPassage->addElem("引导名称", new Input("passage_directins"));
		$sectionPassage->addElem("图片", new UploadCdnImage("image", "pc_prize"));
		$le = new ListElem("passage", $sectionPassage);
		$passageContent = new Div("content");
		$passageContent->addElem("内容类型", new Input("content_type"));
		$sectionPassage->addElem("content", $passageContent);
		$questionBody = new Div("");
		$questionBody->addElem("序号", new Input("number", "number"));
		$questionBody->addElem("问题内容", new Input("question_body ", "number"));
		$questionBody->addElem("问题名称", new Input("answer_name", "number"));
		$answer = new ListElem("answer", new Input(""));
		$questionBody->addElem("答案列表", $answer);
		$question = new ListElem("", $questionBody);
		$sectionPassage->addElem("问题", $question);
		return $le;
	}
	$pass = getPassage();
	$pass->value = $arr["section"][0]["passage"];
	outputhtml("test", $pass->innerHtml);
}

function testTextNode() {
	$span = elem("", [], ["abcdefg", "higklmk", elem("input")]);
	echo $span;
}

function testBlock() {
	$div = new Block("head", "Section", "alert-success");
	$div->addElem("头部",  new Input("head"));
	$div->addElem("说明",  new TextArea("head-instuction", "In this section, you will hear 8 short conversations and ..."));
	$chooseDiv = new Block("haha", "选择题", "alert-warning");
	$chooseDiv->addElem("题号",  new Input("add1"));
	$chooseDiv->addElem("头部",  new Input("add1"));
	$chooseDiv->addElem("问题",  new TextArea("add2", "In this section, you will hear 8 short conversations and ..."));
	$answerDiv = new Input("haha");
	$chooseDiv->addElem("答案", new ListElem("add3", $answerDiv));
	$div->addElem("选择题", new ListElem("add3", $chooseDiv));
	$form = new Form("writing_head");
	$form->addElem("听力头部", new Input("writing_direction"));
	$form->addElem("Section", new ListElem("add3", $div));
	//outputhtml("test", $form->innerHtml);	
	return $form;
}

function getTranslation() {
	$form = new Form("translation");
	$form->addElem("翻译思路", new TextArea("thought", "本文为说明文，简要介绍了中国的旅游业。语言风格应是较正式的说明性语言，主要时态应使用一
般现在时，也可适当使用一般过去时和现在完成时，使得译文丰富多变。"));
	$key_word_div = new Form("");
	$key_word_div->addElem("词汇", new Input("word"));
	$key_word_div->addElem("翻译", new Input("means"));
	$form->addElem("关键词译法", new ListElem("key-word", $key_word_div));
	$form->addElem("翻译标准", new ListElem("standard", new Input("", "text", "", "large")));
	$example = new Block("example", "", "alert-info");
	$example->addElem("正文", new TextArea("example", "With the improvement of living standards, taking a vacation is playing..."));
	$example->addElem("精彩点评", new ListElem("comment", new TextArea("example")));
	$form->addElem("高分译文", $example);
	$form->addElem("翻译技巧", new TextArea("skill", "汉语句子的主语较为灵活，不一定就是动作的执行者，而且也不限于某几种词性。相比较而言，英语句子的主语就只能是名词、代词、非谓语动词、主语从句等，而且一般是动作的执行者。"));
	return $form;
}

function testForm() {
	$form = new Form("writing_head");
	$form->addElemBr("Part头", new Input("writing_head"));
	$form->addElemBr("说 明", new TextArea("writing_direction"));
	$form->addElemBr("图 片", new UploadCdnImage("writing_image", "pc_prize"));

	$nav = new Nav("nav");
	$nav->addElem("Translation", getTranslation());
	$nav->addElem("写作", $form);
	$nav->addElem("听力", testBlock());
	outputhtml("test", $nav->innerHtml);
} 

testForm();
#testClone();
#echo date("Ymd H:i:s", time());
