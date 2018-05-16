<?php
class Table {
	var $t = null;
	var $tHead = null;
	var $titleRow = null;
	var $tBody = null;
	var $valueScript = [];
	var $data = array();
	var $c = null;
	var $m = null;

	public function __construct($attr = []) {
		$this->titleRow = elem("tr");
		$this->tHead = elem("thead", [], $this->titleRow);
		$this->tBody = elem("tbody");
		$this->t = elem("table", $attr, [$this->tHead, $this->tBody]);
		global $c;
		global $m;
		$this->c = $c;
		$this->m = $m;
	}

	public function insertHeadRow() {

	}

	public function insertBodyRow() {

	}

	public function setData($data, $page = 0, $pageSize = 0) {
		$begin = $page * $pageSize;
		$data_size = count($data);
		$length = $pageSize > 0? $pageSize: $data_size;
		$length = $begin + $length > $data_size ? $data_size - $begin:$length;
		$this->data = array_slice($data, $begin, $length);
		for($i = 0; $i < $length; $i++) {
			$attr = [];
			if($i & 1) {
				$attr = ["style"=>"background:#F8F8F8;"];
			}
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
		return $this->t;
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
}
?>