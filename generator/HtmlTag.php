<?php
/*
 * @author Airmanbzh
 */
namespace HtmlGenerator;

if (!defined('ENT_HTML5'))
{
	define('ENT_HTML5', 48);
}

class HtmlTag extends Markup
{
    /** @var int The language convention used for XSS avoiding */
    public static $outputLanguage = ENT_HTML5;
    private $innerHtml = null;

    public function __get($property) {
        if($property == "tag") {
            return $this->$property;
        }
        if($property == "innerHtml") {
            $innerHtml = "";
            foreach($this->content as $v) {
                $innerHtml .= $v->toString();
            }
            return $innerHtml;
        }
    }

    public function __set($property, $value) {
        if($property == "innerHtml") {
            $this->content = [];
            $this->text($value);
        }
    }

    protected $autocloseTagsList = array(
        'img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param'
    );

    /**
     * Shortcut to set('id', $value)
     * @param string $value
     * @return HtmlTag instance
     */
    public function id($value)
    {
        return $this->set('id', $value);
    }

    /**
     * Add a class to classList
     * @param string $value
     * @return HtmlTag instance
     */
    public function addClass($value)
    {
        if (!isset($this->attributeList['class']) || is_null($this->attributeList['class'])) {
            $this->attributeList['class'] = array();
        }
        $this->attributeList['class'][] = $value;
        return $this;
    }

    /**
     * Remove a class from classList
     * @param string $value
     * @return HtmlTag instance
     */
    public function removeClass($value)
    {
        if (!is_null($this->attributeList['class'])) {
            unset($this->attributeList['class'][array_search($value, $this->attributeList['class'])]);
        }
        return $this;
    }
}
