<?php

require_once("simple_html_dom.php");
require_once("Zend/Dom/Query.php");       

class htmlElementRetouch {

    public $item = array();
    public $hit = array();
    public $name = array();
    public $context_data;

    function __construct() {
        
    }
    
    /*
     * 置換処理を実行
     */
    public function replace() {
        //置換。。。
    }

    /*
     * 比較処理を実行
     */
    public function compare() {
        //HTMLデータをパース        
        $domQuery = new Zend_Dom_Query();
        $domQuery->setDocument($this->context_data, 'UTF-8');

        // 検索対象分ループ
        for ($i = 0; $i < count($this->item); $i++) {
            //検索
            $selectors = $this->_createSelector($this->item[$i]);
            $check = false;
            foreach($selectors as $selector){
                $result = $domQuery->queryXpath($selector);
                if($result->count() > 0){
                    $check = true;
                }else{
                    $check = false;                
                }
            }
            $this->hit[$i] = $check;
        }
    }

    /*
     * 要素を検索するセレクタを生成する(XPath版)
     */
    function _createSelector($text){
        $root = str_get_html($text);
        $selector = "";
        $select_array = array();
        $this->_getNodesInfoXpath($root, $selector, $select_array);
        return $select_array;
    }

    /*
     * 要素を検索するセレクタを生成する(CSSセレクタ版)
     */
    function _createSelectorCss($text){
        $root = str_get_html($text);
        $selector = "";
        $select_array = array();
        $this->_getNodesInfo($root, $selector, $select_array);
        return $select_array;
    }

    /*
     * 要素をCSSセレクタに変換
     */
    function _getNodesInfo($nodes, &$selector, &$select_array){
        foreach ($nodes->childNodes() as $node) {
            $se = " $node->tag";
            foreach($node->getAllAttributes()  as $name => $value){
                if($node->tag === "img" && $name === "src"){
                    continue;
                }
                $se .= sprintf('[%s="%s"]', $name, $value);
            }
            if($node->has_child()) {
                $selector .= $se;
                $this->_getNodesInfo($node, $selector, $select_array);
            }else{
                $selector .= $se;
                array_push($select_array, $selector);
                $selector = str_replace($se, '', $selector);
            }
        }
    }
    
    /*
     * 要素をXPathに変換
     */
    function _getNodesInfoXpath($nodes, &$selector, &$select_array){
        foreach ($nodes->childNodes() as $node) {
            $node_tag = '/'.$node->tag.$this->_createAtrributeXpath($node);
            $selector .= $node_tag;
            array_push($select_array, '/'.$selector);
            if($node->has_child()) {
                $this->_getNodesInfoXpath($node, $selector, $select_array);
            }else{
                $selector = preg_replace('('.preg_quote($node_tag).'$)', '',  $selector);               
                $this->_goBackReplace($node, $selector);
            }
        }
    }
    
    /*
     * 親要素を削除
     */
    function _goBackReplace($node, &$selector){
        if(!is_null($node->parent())){
            if(strlen($node->nextSibling()) === 0 && $node->parent()->tag != 'root'){
                $selector = preg_replace(
                        '(\/'.preg_quote($node->parent()->tag.$this->_createAtrributeXpath($node->parent())).'$)',
                        '',
                        $selector);
                $this->_goBackReplace($node->parent(), $selector);
            }           
        }
    }
    
    /*
     * 要素用のXpathを作成する
     */
    function _createAtrributeXpath($node){
        $selector = "";
        foreach($node->getAllAttributes()  as $name => $value){
            $selector .= sprintf('[@%s="%s"]', $name, $value);
        }
        return $selector;
    }
}
