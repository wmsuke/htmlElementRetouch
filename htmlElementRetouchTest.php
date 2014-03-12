<?php

require_once dirname(__FILE__) . '/../../../Lib/htmlElementRetouch.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-05 at 07:40:12.
 */
class htmlElementRetouchTest extends PHPUnit_Framework_TestCase {

    /**
     * @var htmlElementRetouch
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new htmlElementRetouch;
        $this->object->context_data = <<<EOT
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noodp" />
    <meta name="robots" content="index,follow" />
    <meta name="description" content="testテスト試み" />
    <title>aaa</title>
</head>
<body>
    <div>
        <li name="robots" content="index,follow">content あり</li>
        <span id="span1">aaaaa</span>
        <div class="class1">
            <a href="/" name="pagetop">
                <img src="img/img_r0001.gif" alt="IMG" width="100" height="20"/>
            </a>
        </div>
        <p class="font">content なし</p>
        <div id="div1">
            <span>おおお</span>
        </div>
    </div>
</body>
</html>
EOT;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers htmlElementRetouch::compare
     */
    public function testCompare() {
        // 事前準備
        $this->object->item = [
            '<meta name="description" content="testテスト試み" />',
            <<<EOT
<div>
    <li name="robots" content="index,follow">content あり</li>
    <span id="span1">aaaaa</span>
    <div class="class1">
        <a href="/" name="pagetop">
            <img src="img/img_r0001.gif" alt="IMG" width="100" height="20"/>
        </a>
    </div>
    <p class="font">content なし</p>
    <div id="div1">
        <span>おおお</span>
    </div>
</div>
EOT
        ];
        $this->object->name = [
            "item1",
            "item2"
        ];        
        $this->object->compare();
        
        $this->assertEquals(true, $this->object->hit[0]);
        $this->assertEquals(true, $this->object->hit[1]);
    }

    /**
     * @covers htmlElementRetouch::_createSelector
     */
    public function test_createSelector() {
        // 要素一つ
        $this->assertEquals(
                ["//span"],
                $this->object->_createSelector("<span>aaaa</span>")
            );

        // クラスを付ける
        $this->assertEquals(
                ['//span[@name="robots"][@content="index,follow"]'],
                $this->object->_createSelector('<span name="robots" content="index,follow">aaaa</span>')
            );
        
        // 並列要素
        $this->assertEquals(
                ["//li", "//li", "//li"],
                $this->object->_createSelector("<li>aaa</li><li>bbb</li><li>ccc</li>")
            );
        
        // 親子
        $this->assertEquals(
                ["//ul+li", "//li", "//li"],
                $this->object->_createSelector("<ul><li>aaa</li><li>bbb</li><li>ccc</li></ul>")
            );
    }

}
