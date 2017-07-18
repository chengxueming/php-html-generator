<?php
function elem($name, $attrs = [], $childrens = []){
    $e = HtmlGenerator\HtmlTag::createElement($name);
    foreach($attrs as $key => $v) {
        $v = preg_replace("/\"/", "'", $v);
        $e->set($key, $v);
    }
    $childrens = is_array($childrens)?$childrens:[$childrens];
    foreach($childrens as $key=>$child) {
        if(is_string($child)) {
            $e->text($child);
        }else {

            $e->addElement($child);
        }
    }
    return $e;
}

if ( ! function_exists('safeSetArr')) {
    function safeSetArr(&$args, $set) {
        foreach($set as $key=>$v) {
            if(!isset($args[$key])) {
                $args[$key] = $v;//GET
            }
        }
    }
}

function titleSearch($title, $searchKey, $ajaxParam, $attrs = []) {
    if(!isset($attrs["style"])) {
        $attrs["style"] = [];
        $attrs["style"][] = "width:60px;";
    }
    if(!isset($attrs["style"])) {
        $attrs["value"] = "";
    }
    $attrs["name"] = $searchKey;
    $attrs["id"] = "search_".$searchKey;
    $ajaxParam[$searchKey] = ["$('#${attrs['id']}').val()"];
    //$click_js = "var a = $('#${attrs['id']}').val();if(a == '' || isNaN(parseInt(a))) return;".ajax($ajaxParam);
    $click_js = ajax($ajaxParam);
    return [$title, elem("label", ["for"=> $searchKey]), elem("br"), elem("input", $attrs), elem("br"), elem("button", ["onclick"=>$click_js], "给我搜")];
}

function arrayRemove(&$arr, $key) {
    $param = $arr[$key];
    unset($arr[$key]);
    return $param;
}

function ajax($args, $successJs = "", $baseUrlParam, $break="&amp;") {
    $c = arrayRemove($baseUrlParam, "c");
    $m = arrayRemove($baseUrlParam, "m");
    $baseurl = ci_link($c, $m,  $baseUrlParam, $break);
    $ajaxArgs = isset($args["ajax"])?$args["ajax"]:[];
    unset($args["ajax"]);
    safeSetArr($ajaxArgs, ["type"=>"GET", "async"=>["true"], "dataType"=>"json"]);
    if ( ! function_exists('toJsMapInner')) {
        function toJsMapInner($args) {
            $param = "";
            foreach($args as $k => $v) {
                if(is_array($v)) {
                    $param .= "$k:${v[0]}, ";
                }else {
                    $param .= "$k:'$v', ";
                }
            }
            return $param;
        }
    }

    $ajaxParam = toJsMapInner($ajaxArgs);
    $urlParam = toJsMapInner($args);

    $_ajax =<<<EOF
    $.ajax({
            url:'$baseurl',
            $ajaxParam
            data:{
            $urlParam
            },
            success:function(data,textStatus,jqXHR){
                $successJs
            },
           });
EOF;
    return $_ajax;
}

function a($c, $m, $args = [], $text) {
    return elem("a", ["href"=>ci_link($c, $m, $args)], $text);
}

function ci_link($c, $m, $args = [], $break="&amp;") {
    $root = "http://mis.iciba.com/msg_admin/www/index.php";
    $arg = "";
    foreach($args as $k => $v) {
        $arg .= "$break$k=$v";
    }
    $link = "${root}?c=$c${break}m=$m$arg";
    return $link;
}

function button($c, $m, $args = [], $text) {
    $a = a($c, $m, [], $text);

    $e = elem("button", [
    "style"=> [
        "width:100px;",
        "height:30px;"
    ]], $a);
    return $e;
}
?>