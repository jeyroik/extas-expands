<?php
namespace tests;

use extas\components\expands\Expander;
use extas\components\expands\ExpandingBox;
use extas\components\expands\ExpandRequired;
use extas\interfaces\expands\IExpandingBox;
use PHPUnit\Framework\TestCase;

class ExpanderTest extends TestCase
{
    public function testGetExpandingBox()
    {
        $box = Expander::getExpandingBox('root', 'box');
        $this->assertTrue($box instanceof IExpandingBox);
        $this->assertEquals('root', $box->getRoot());
        $this->assertEquals('box', $box->getName());

    }

    public function testExpandingBox()
    {
        $box = new ExpandingBox();

        $box->setRoot('root');
        $this->assertEquals('root', $box->getRoot());
        $this->assertFalse($box->isPacked());

        $box->setData(['data']);
        $this->assertEquals(['data'], $box->getData());

        $box->pack();
        $this->assertTrue($box->isPacked());

        $box->unpack();
        $this->assertFalse($box->isPacked());

        $box->setExpand([]);
        $this->assertEmpty($box->getExpand());

        $box->addExpand('added');
        $this->assertEquals(['added'], $box->getExpand());

        $box->addToValue('test', 'is ok');
        $this->assertEquals(['test' => 'is ok'], $box->getValue());
    }

    public function testExpandRequired()
    {
        $required = new ExpandRequired();
        $required->setAccept(['*']);
        $required->setRoutes(['/']);
        $this->assertEquals(['*'], $required->getAccept());
        $this->assertEquals(['/'], $required->getRoutes());
    }
}
