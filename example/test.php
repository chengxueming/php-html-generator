<?php
require "../autoload.php";
function testDivCondation()
{
    $img = [
        [
            "url"      => "a girl ride on a horse",
            "type"     => 1,
            "jump_url" => [
                "praise"  => "a url to post a praise",
                "comment" => "a url to post a comment",
                "name"    => [
                    "frst"   => "jhon",
                    "last"   => "ham",
                    "type"   => 1,
                    "addion" => [
                        "before" => 12,
                        "after"  => 13,
                    ],
                ],
                "more"    => [
                    "ansewer1",
                    "ansewer2",
                    "ansewer3",
                    "ansewer4",
                    "ansewer5",
                ],
            ],
        ],
        [
            "url"      => "a girl ride on a horse",
            "type"     => 1,
            "jump_url" => [
                "praise"  => "a url to post a praise",
                "comment" => "a url to post a comment",
                "name"    => [
                    "frst"   => "jhon",
                    "last"   => "ham",
                    "type"   => 1,
                    "addion" => [
                        "before" => 12,
                        "after"  => 13,
                    ],
                ],
                "more"    => [
                    "ansewer1",
                    "ansewer2",
                    "ansewer3",
                    "ansewer4",
                    "ansewer5",
                ],
            ],
        ],
    ];
    $div = new Div("");
    $div->addElem("内容", new Input("url"));
    $div->addElem("类型", new Select("type", [0 => "只读", 1 => "可以评论"]));
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
    $divname->addElem("TYPE", new Select("type", [0 => "normal", 1 => "not"]));
    $divname->addElem("ADDTION", $divaddion, ["TYPE" => 1]);
    $divjump->addElem("MORE", new ListElem("more", new Input("")));
    $le        = new ListElem("", $div);
    $le->value = $img;
    outputhtml("test", $le->innerHtml, "normal");
}

function testListDivCondation()
{
    $imgList = [
        [
            "url"  => "a boy ride on a horse",
            "type" => 0,
        ],
        [
            "url"      => "a girl ride on a horse",
            "type"     => 1,
            "jump_url" => [
                "praise"  => "a url to post a praise",
                "comment" => "a url to post a comment",
            ],
        ],
        [
            "url"  => "a boy ride on a horse",
            "type" => 0,
        ],
    ];
    $div = new Div("");
    $div->addElem("内容", new Input("url"));
    $div->addElem("类型", new Select("type", [0 => "只读", 1 => "可以评论"]));
    $divjump = new Div("jump_url");
    $divjump->addElem("点赞", new Input("praise"));
    $divjump->addElem("评论", new Input("comment"));
    $div->addElem("功能", $divjump, ["类型" => 1]);
    $listElem        = new ListElem("", $div);
    $listElem->value = $imgList;
    outputhtml("test", $listElem->innerHtml);
}

function testComponents()
{

    $t = new EditTable(["width" => "70%", "align" => "center", "cellspacing" => "0", "cellpadding" => "6"]);
    $t->setData([
        "movie_name"   => "穆赫兰道",
        "origin_price" => 500,
        "sale_price"   => 200,
        "type"         => "4,5",
    ]);
    $t->row("电影名称", new Input("movie_name"));
    $t->row("票价", new Input("origin_price", "number", "$"));
    $t->row("售价", new Input("sale_price", "number", "$"));
    $t->row("类型", new CheckBox("type", [1 => "爱情", 2 => "恐怖", 3 => "童话", 4 => "推理", 5 => "悬疑"]));
    $t->submit("save", "index", [], "保存");
    outputhtml("test", $t->render());
}

function testListTable()
{
    $t = new Table();
    $t->setData(
        [
            ["movie_name"  => "穆赫兰道",
                "origin_price" => 500,
                "sale_price"   => 200,
                "type"         => "4,5",
            ], [
                "movie_name"   => "银河护卫队",
                "origin_price" => 300,
                "sale_price"   => 100,
                "type"         => "4,5",
            ],
        ]
    );
    $t->column("movie_name", "商品ID");
    $t->column("origin_price", "票价($)");
    $t->column("sale_price", "原价($)");
    $t->column(function ($row) {return button("class", "method", [], "编辑");}, "编辑");
    outputhtml("test", $t->render());
}

function outputhtml($file, $code, $type = "boot")
{
    $boot_head = <<<EOF
        <meta charset="utf-8" />

        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type = "text/javascript" src="static/js/common.js"></script>
        <style type="text/css" src="common.css"></style>
        <style type="text/css" src="./example/static/theme.css"></style>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

EOF;
    $normal_head = <<<EOF
        <meta charset="utf-8" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
        <script type = "text/javascript" src="static/js/common.js"></script>
        <style type="text/css" src="common.css"></style>
EOF;
    #$head.$t->render();
    if ($type == "normal") {
        $boot_head = $normal_head;
    }
    $code .= file_get_contents("./static/edittool.html");
    file_put_contents("./$file.html", $boot_head . $code);
}

function testIncrPropertys()
{
    #$input = elem("input", ["id"=>"input12324", "name"=>"input1312", "onclick"=>'"#input12324"']);
    #incrPropertys(["id", "name"], $input);
    #var_dump($input->tag);
    $scr = elem("script", [], '"#input12324"');
    incrPropertys([], $scr);
    $scr;
}

function testInnerHtml()
{
    $scr            = elem("script", [], "hello");
    $scr->innerHtml = "good apple";
    $scr;
}

function testListDivList()
{
    $div  = new Div("div");
    $init = [
        ["www.baidu.com", "www.baidu.com", "www.baidu.com", "www.baidu.com"],
        ["www.baidu.com", "www.baidu.com", "www.baidu.com", "www.baidu.com"],
    ];
    $le             = new ListElem("a", new Input("", "text"));
    $outlist        = new ListElem("outlist", $le);
    $outlist->value = $init;
    outputhtml("test", $outlist->innerHtml);
}

function tsetExam()
{
    $json = file_get_contents("./listening.txt");
    $arr  = json_decode($json, true);
    $part = new Div("");
    $part->addElem("类型", new Input("type", "text"));
    $part->addElem("头部", new Input("head", "text"));
    function getSection()
    {
        function getPassage()
        {
            $sectionPassage = new Div("");
            $sectionPassage->addElem("名称", new Input("passage_name"));
            $sectionPassage->addElem("引导名称", new Input("passage_directins"));
            $sectionPassage->addElem("图片", new UploadCdnImage("image", "pc_prize"));
            $le             = new ListElem("passage", $sectionPassage);
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

function testPassage()
{
    $json = file_get_contents("./listening.txt");
    $arr  = json_decode($json, true);
    function getPassage()
    {
        $sectionPassage = new Div("");
        $sectionPassage->addElem("名称", new Input("passage_name"));
        $sectionPassage->addElem("引导名称", new Input("passage_directins"));
        $sectionPassage->addElem("图片", new UploadCdnImage("image", "pc_prize"));
        $le             = new ListElem("passage", $sectionPassage);
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
    $pass        = getPassage();
    $pass->value = $arr["section"][0]["passage"];
    outputhtml("test", $pass->innerHtml);
}

function testTextNode()
{
    $span = elem("", [], ["abcdefg", "higklmk", elem("input")]);
    $span;
}

function testBlock()
{
    $div = new Block("head", "Section", "alert-success");
    $div->addElem("头部", new Input("head"));
    $div->addElem("说明", new TextArea("head-instuction", "In this section, you will hear 8 short conversations and ..."));
    $chooseDiv = new Block("haha", "选择题", "alert-warning");
    $chooseDiv->addElem("题号", new Input("add1"));
    $chooseDiv->addElem("头部", new Input("add1"));
    $chooseDiv->addElem("问题", new TextArea("add2", "In this section, you will hear 8 short conversations and ..."));
    $answerDiv = new Input("haha");
    $chooseDiv->addElem("答案", new ListElem("add3", $answerDiv));
    $div->addElem("选择题", new ListElem("add3", $chooseDiv));
    $form = new Form("writing_head");
    $form->addElem("听力头部", new Input("writing_direction"));
    $form->addElem("Section", new ListElem("add3", $div));
    //outputhtml("test", $form->innerHtml);
    return $form;
}

function testForm()
{
    // $form = new Form("writing_head");
    // $form->addElemBr("Part头", new Input("writing_head"));
    // $form->addElemBr("说 明", new TextArea("writing_direction"));
    // $form->addElemBr("图 片", new UploadCdnImage("writing_image", "pc_prize"));
    $form = new Form("paper");
    $form->addElem("试卷标题", new Input("paper_tilte"));
    $form->addSubmitBtn();
    $nav = new Nav("试卷内容");
    $nav->addElem("Part I Writing", getWriting());
    $nav->addElem("Part III Reading Comprehension", getReading());
    // $nav->addElem("Part IV Writing", new Form(""));
    // $nav->addElem("Part V Writing", new Form(""));
    $nav->addElem("Part VI Translation", getTranslation());
    $form->addElem("", $nav);
    $head = "<br>"."&nbsp;&nbsp;&nbsp;" . a("pc_prize", "save", [], "试卷列表");
    $form->submit("pc_prize", "save", []);
    $form->valueScript;
    outputhtml("testTemp", $head . $form->innerHtml);
}

function testListElem()
{
    $form = new Form("writing");
    $en   = new Block("english", "选择题");
    $en->addElem("重点", new TextArea("important"));
    $en->addElem("其他", new TextArea("other"));

    $ch = new Block("english", "填空题");
    $ch->addElem("词汇", new Input(""));
    $ch->addElem("翻译", new Input(""));

    $list = new ListElem("testTmep", $ch);
    $form->addElem("标题", $list);
    outputhtml("testTemp", $form->innerHtml);
}

function testFormValue()
{
    $data = [
        "staff"   => 231241243,
        "pip"     => "dsfasfsdf",
        "english" =>
        [
            "important" => "abcdefg",
            "other"     => "ag",
        ],
        "god"     =>
        [
            "important" => "ag",
            "other"     => "dsfadfasd",
        ],
    ];
    $form = new Form("writing");
    $form->addElem("重点", new TextArea("staff"));
    $form->addElem("其他", new Input("pip"));
    $form->addSubmitBtn();
    $en = new Block("english", "选择题");
    $en->addElem("重点", new TextArea("important"));
    $en->addElem("其他", new TextArea("other"));
    $form->addElem("超重点", $en);
    $en = new Block("god", "选择题");
    $en->addElem("重点", new TextArea("important"));
    $en->addElem("其他", new TextArea("other"));
    $form->addElem("超重点", $en);
    $form->value = $data;
    $form->submit("c", "m", []);
    outputhtml("testTemp", $form->innerHtml);
}

function testTextArea()
{
    $textArea        = new TextArea("agsdaf");
    $textArea->value = "asgbsafsaf";
    $textArea->innerHtml["id"] . "\n";
    $textArea->valueScript;
    outputhtml("testTemp", $textArea->innerHtml);
}

function testBlockNew()
{
    $v = [
        "important" => 2132,
        "pip"       => "sdfasfs",
    ];
    $en = new Block("english", "选择题");
    $en->addElem("重点", new TextArea("important"));
    $en->addElem("其他", new TextArea("pip"));
    $en->value = $v;
    return $en;
}

function testNavBar()
{
    $nav = new Nav("nav");
    $nav->addElem("Part I Writing", testFormValue());
    $nav->addElem("Part III Writing", testBlockNew());
    outputhtml("testTemp", $nav->innerHtml);
}

function testListElemValue()
{
    $l = [
        "example" => "asfsdaf",
        "comment" => [
            [
                "important" => "abcdefg",
                "other"     => "ag",
            ],
            [
                "important" => "abcdefg",
                "other"     => "ag",
            ],
            [
                "important" => "abcdefg",
                "other"     => "ag",
            ],
            [
                "important" => "abcdefg",
                "other"     => "ag",
            ],
        ],
    ];
    $example = new Block("example", "", "alert-info");
    $example->addSubmitBtn();
    $example->addElem("正文", new TextArea("example", "With the improvement of living standards, taking a vacation is playing..."));
    $en = new Block("english", "选择题");
    $en->addElem("重点", new TextArea("important"));
    $en->addElem("其他", new TextArea("other"));
    $example->addElem("精彩点评", new ListElem("comment", $en));
    $example->value = $l;
    $example->valueScript;
    $example->submit("pc_prize", "save", []);
    outputhtml("testTemp", $example->innerHtml);
}

function getWriting()
{
    $form = new Form("写作");

    $questionTrans = new TextArea("题目译文", "指令：在此部分，要求你用30 分钟写一篇关于创新的短论文...", true);
    $form->addElem("题目译文", $questionTrans);
    $form->addElem("审题构思", new TextArea("正文", "2012 年12 月，六级写作首次考查创新这一话题，要求以Man and Computer 为题写一篇短论文。"));
    $form->addElem("写作提纲", new UploadCdnImage("写作提纲", "pc_prize"));


    $example = new Block("");
    $example->addElem("高分范文", new TextArea("正文", "Nowadays, with the quickening pace of urban life and ever-increasing...", true));
    $example->addElem("范文译文", new TextArea("中文翻译", "如今，随着都市生活节奏的加快和工作压力的不断增加，人们非常重视创新。显而易见"));
    $example->addElem("道长点评", new TextArea("道长点评", "本文共三段、十句、175 词。 首段为观点陈述段，共三句。首句 交代背景，使用了介词短语放在句 首做状语、并列结构")); 
    $form->addElem("高分范文和点评", new ListElem("高分范文和点评", $example));

    $en = new Form("");
    $en->addElem("词汇", new Input("词汇"));
    $en->addElem("翻译", new Input("翻译"));
    $form->addElem("词汇点拨", new ListElem("词汇点拨", $en));

    $en = new Form("");
    $en->addElem("词汇", new Input("词汇"));
    $en->addElem("翻译", new Input("翻译"));
    $form->addElem("精彩表达", new ListElem("精彩表达", $en));

    $en = new Form("");
    $en->addElem("英文", new TextArea("英文"));
    $en->addElem("中文", new TextArea("中文"));
    $form->addElem("佳句拓展", new ListElem("佳句拓展", $en));

    $en = new Form("");
    $en->addElem("英文", new TextArea("英文"));
    $en->addElem("中文", new TextArea("中文"));
    $form->addElem("万能模板", new ListElem("万能模板", $en));
    return $form;
}

function getTranslation()
{
    $form = new Form("翻译");
    $form->addElem("翻译思路", new TextArea("翻译思路", "本文为说明文，简要介绍了中国的旅游业。语言风格应是较正式的说明性语言，主要时态应使用一
般现在时，也可适当使用一般过去时和现在完成时，使得译文丰富多变。"));
    $key_word_div = new Form("");
    $key_word_div->addElem("词汇", new Input("词汇"));
    $key_word_div->addElem("翻译", new Input("翻译"));
    $form->addElem("关键词译法", new ListElem("关键词译法", $key_word_div));
    $form->addElem("翻译标准", new ListElem("翻译标准", new Input("", "text", "", "large")));
    $example = new Block("高分译文", "", "alert-info");
    $example->addElem("正文", new TextArea("正文", "With the improvement of living standards, taking a vacation is playing..."));
    $example->addElem("精彩点评", new ListElem("精彩点评", new TextArea("example")));
    $form->addElem("高分译文", $example);
    $form->addElem("翻译技巧", new TextArea("翻译技巧", "汉语句子的主语较为灵活，不一定就是动作的执行者，而且也不限于某几种词性。相比较而言，英语句子的主语就只能是名词、代词、非谓语动词、主语从句等，而且一般是动作的执行者。"));
    return $form;
}

function getReading()
{
    function getSection()
    {
        $form = new Block("Passage", "Passage");
        $form->addElemBr("Passage名", new Input("PassageName", "text", "Passage One"));
        $head = new Block("文章概述", "文章概述", "alert-warning");
        $head->addElem("题源分析", new UploadCdnImage("题源分析", "pc_prize"));
        $head->addElem("结构剖析", new UploadCdnImage("结构剖析", "pc_prize"));
        $head->addElem("选项分类", new UploadCdnImage("选项分类", "pc_prize"));
        $form->addElem("文章概述", $head);
        $f    = new Block("语篇分析", "", "alert-warning");
        $head = new Block("解析", "");
        $head->addElem("正文", new TextArea("正文", "①What a waste of money!"));
        $head->addElem("翻译", new TextArea("翻译", "①多浪费钱"));
        $f->addElem("解析", $head);
        $key_word_div = new Form("");
        $key_word_div->addElem("词汇", new Input("词汇", "text", "recruiter"));
        $key_word_div->addElem("翻译", new Input("翻译", "text", "n. 招聘者"));
        $l = new ListElem("词汇点拨", $key_word_div);
        $f->addElem("词汇点拨", $l);
        $form->addElem("语篇分析", new ListElem("语篇分析", $f));
        function getChoose() {
        	$head = new Block("", "选择题", "alert-success", "What is the author’s opinion of going to university?");
        	$head->addElemBr("序号", new Input("序号", "text", 51));
        	$head->addElem("问题", new Input("问题"));
        	$head->addElem("问题翻译", new TextArea("问题翻译"));
        	$ansewer = new Form("");
        	$ansewer->addElem("答案", new Input("答案", "text", "A) It is worthwhile after all."));
        	$ansewer->addElem("翻译", new Input("翻译", "text", "终究还是值得的。"));
        	$head->addElem("答案翻译", new ListElem("答案翻译", $ansewer));
        	$head->addElem("答案", new Input("答案"));
        	$head->addElem("解析", new TextArea("解析", "解析：该题的答案在第三段，前两段是人们对大学的抱怨..."));
        	$head->addElem("点评", new TextArea("点评", "该文共五段，第一题答案出现在第三段的情况比较少见，需要重视。"));
        	return $head;
        }
        function getBlank() {
        	$head = new Block("", "完形填空", "alert-success");
        	$head->addElem("英文", new TextArea("英文"));
        	$head->addElem("翻译", new TextArea("翻译"));
        	$head->addElem("答案", new Input("答案"));
        	$head->addElem("解析", new TextArea("解析"));
        	return $head;
        }
        function getCompBlank() {

        }
        function getLine() {
        	$head = new Block("", "连线题", "alert-success");
        	$head->addElem("序号", new Input("序号"));
        	$head->addElem("答案", new Input("答案"));
        	$head->addElem("解析", new TextArea("解析"));
        	return $head;
        }
        $form->addElem("试题精解", new ListElem("试题精解", ["选择题"=>getChoose(), "连线题"=>getLine(), "填空题"=>getBlank()], 1, "试题精解"));
        $passages = new ListElem("Passage", $form, 1, "Passage");
        $SectionContent = new Block("", "");
        $SectionContent->addElem("Section名", new Input("SectionName"));
        $SectionContent->addElem("Passage", $passages);
        $section = new ListElem("Section", $SectionContent, 1, "Section");
        return $section;
    }
    return getSection();
}

function myform()
{
    $example = new Block("", "");
    $en      = new Block("", "");
    $en->addElem("重点", new TextArea(""));
    $en->addElem("其他", new TextArea(""));

    $example->addElem("英文", $en);
    $example->addElem("中文翻译", new TextArea("中文翻译"));
    $example->addElem("道长点评", new TextArea("道长点评"));
    $form = new Form("");
    $form->addSubmitBtn();
    $l = new ListElem("高分范文和点评", $example);
    $form->addElem("高分范文和点评", $l);
    $form->submit("pc_prize", "save", []);
    outputhtml("testTemp", $form->innerHtml);
}

function myBlock()
{
    $en = new Block("", "");
    $en->addSubmitBtn();
    $en->addElem("重点", new TextArea("重点"));
    $en->addElem("其他", new TextArea("其他"));
    $en->submit("pc_prize", "save", []);
    outputhtml("testTemp", $en->innerHtml);
}

function testMutiList()
{
    $ls = [
        [
            ["重点" => "afgsadfsad",
                "其他"  => "afgsadfsad"],
            "TYPE1",
        ],
        [
            ["重点" => "afgsadfsad",
                "其他"  => "afgsadfsad"],
            "TYPE1",
        ],
        [
            ["中文翻译" => "afgsadf",
                "道长点评"  => "sadfsad"],
            "TYPE2",
        ],
    ];
    $en = new Block("TYPE1", "TYPE1");
    $en->addElem("重点", new TextArea("重点"));
    $en->addElem("其他", new TextArea("其他"));

    $example = new Block("TYPE2", "TYPE2");
    $example->addElem("中文翻译", new TextArea("中文翻译"));
    $example->addElem("道长点评", new TextArea("道长点评"));
    $l = new ListElem("高分范文和点评", ["TYPE1" => $en, "TYPE2" => $example]);
    $l->value = $ls;
    outputhtml("testTemp", $l->innerHtml);
}

// function getPara() {
//     $f = new Block("语篇分析", "");
//     $head = new Block("解析", "");
//     $head->addElem("正文", new TextArea("正文"));
//     $head->addElem("翻译", new TextArea("翻译"));
//     $f->addElem("解析", $head);
//     $key_word_div = new Form("");
//     $key_word_div->addElem("词汇", new Input("词汇"));
//     $key_word_div->addElem("翻译", new Input("翻译"));
//     $l = new ListElem("词汇点拨", $key_word_div);
//     $f->addElem("词汇点拨", $l);
//     $f = new ListElem("语篇分析", $f);
//     outputhtml("testTemp", $f->innerHtml);
// }

#getTranslation();
#ajax2("function(){}", [], "", ["c"=>"a", "m"=>"d"]);

function testClone() {
    $sub = elem("div", ["id"=>2], "subdiv");
    $input = elem("div", ["id"=>1], ["outdiv", $sub]);
    $input2 = clone $input;
    $sub->innerHtml = "changesub";
    echo $input;
}


function testBreadCrumb()
{
    $b = new Breadcrumb();
    $b->addLink("Home", "index.php?c=index&m=home");
    $b->addLink("Trade", "index.php?c=index&m=home");
    $b->addLink("Detail","", true);
    #outputhtml("testTemp", $b());
    return $b();
}

function testHeader() {
    $h = new Header("交易详情");
    outputhtml("testTemp", $h().testBreadCrumb());
    return $h().testBreadCrumb(); 
}

function testTable() {
    $t = new Table();
    $data = [
        ["id"=>"1", "试卷名称"=>"a"],
        ["id"=>"2", "试卷名称"=>"b"],
        ["id"=>"3", "试卷名称"=>"c"],
        ["id"=>"4", "试卷名称"=>"d"],
    ];
    $t->setData($data);
    $t->column("id", "ID");
    $t->column("试卷名称", "试卷名称");
    outputhtml("testTemp", testHeader().$t->render());
}


#testHeader();
#$text = "保存";
#print_r(explode("",$text));
testTable();
