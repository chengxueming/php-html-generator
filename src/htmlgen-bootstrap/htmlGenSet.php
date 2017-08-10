<?php
/**
 * Created by PhpStorm.
 * User: CHENGXUEMING
 * Date: 17-8-7
 * Time: 下午2:05
 */ 

class BaseGen {
	protected $innerHtml = null;
	public function __invoke() {
		return $this->innerHtml;
	}
}

class Content {

}

class Header  extends BaseGen  {
	public function __construct($title) {
		$this->innerHtml = elem("div", ["class"=>"header"], elem("h1", ["class"=>["page-title"]], $title));
	}
}

class Breadcrumb extends BaseGen {
	private $valueElem = [];
	public function __construct() {
		$this->innerHtml = elem("ul", ["class"=>"breadcrumb"]);
	}

	public function addLink($title, $href = "", $active = false) {
		$li = elem("li");
		$a = elem("a", ["href"=>$href], $title);
		if(empty($href)) {
			$a = elem("", [], $title);
		}
		$li->addElement($a);
		if(count($this->valueElem) > 0) {
			end($this->valueElem)->addElement(elem("span", ["class"=>"divider"]));
		}
		if($active) {
			$li->addClass("active");
		}
		array_push($this->valueElem, $li);
		$this->innerHtml->addElement($li);
	}
}


class Table {
	var $t = null;
	var $tHead = null;
	var $titleRow = null;
	var $tBody = null;
	var $valueScript = [];
	var $data = array();
	var $c = null;
    var $m = null;
    var $nav_bar = null;
    var $script = "";
    var $row_fluid = "";

	public function __construct($attr = []) {
		$this->titleRow = elem("tr");
		$this->tHead = elem("thead", [], $this->titleRow);
        $this->tBody = elem("tbody");
        $attr = array_merge($attr, ["class"=>"table"]);
        $this->t = elem("table", $attr, [$this->tHead, $this->tBody]);
        $out = elem("div", ["class"=>"well"], $this->t);
        $this->row_fluid = elem("div", ["class"=>"row_fluid"], $out);
        $out = elem("div", ["class"=>"container-fluid"], $this->row_fluid);
        $this->nav_bar = "";
        $this->t = $out;
        $this->script = elem("script");
		global $c;
		global $m;
		$this->c = $c;
		$this->m = $m;
    }

    private function get_nav_bar() {
        return elem("div", ["style"=>["text-align:left"], "pagination"=>"pagination_new", "startpage"=>"1", "currentpage"=>"1", "totalpage"=>1]);
    }

	public function insertHeadRow() {

	}

	public function insertBodyRow() {

	}

	public function setData($data, $page = 0, $pageSize = 0, $url = "") {
        $data_size = count($data);
        if($pageSize > 0) {
            $this->nav_bar = $this->get_nav_bar();
            $this->nav_bar["totalpage"] = ceil($data_size/$pageSize);
            $this->nav_bar["currentpage"] = $page;
            $this->row_fluid->addElement($this->nav_bar);
            $this->script->innerHtml =<<<JS
            var callback = function(page_id) {
            window.location = "$url&page=" + page_id;
};
            paginationInit(callback);
            $("div[class='pagination']").children("ul").addClass("pagination");
JS;
        }
        $page = $page - 1;
		$begin = $page * $pageSize;
        $length = $pageSize > 0? $pageSize: $data_size;
		$length = $begin + $length > $data_size ? $data_size - $begin:$length;
		$this->data = array_slice($data, $begin, $length);
		for($i = 0; $i < $length; $i++) {
			$attr = [];
			$r = elem("tr", $attr);
			$this->tBody->addElement($r);
		}
	}

	public function getTHead() {
		return $this->tHead;
	}

	public function getTBody() {
		return $this->tBody;
	}

    public function render() {
		return $this->t.$this->script;
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
			current($tBodyRows) -> addElement(elem("td", $cur_attr, $innerHtml));
			next($tBodyRows);
		}
	}
}
?>
