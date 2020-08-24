<?php
namespace tests\expands;

use extas\components\expands\Expander;
use extas\components\expands\ExpandingBox;
use extas\components\expands\ExpandRequired;
use extas\components\http\TSnuffHttp;
use extas\components\plugins\TSnuffPlugins;
use extas\components\protocols\ProtocolExpand;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\interfaces\expands\IExpandingBox;
use tests\expands\misc\PluginBox;
use tests\expands\misc\PluginDispatch;
use tests\expands\misc\PluginException;
use tests\expands\misc\PluginRoot;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Class ExpanderTest
 *
 * @package tests\expands
 * @author jeyroik@gmail.com
 */
class ExpanderTest extends TestCase
{
    use TSnuffRepositoryDynamic;
    use TSnuffPlugins;
    use TSnuffHttp;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffPlugins();
    }

    public function testExpandDispatching()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test'
        ]);

        $this->createSnuffPlugin(PluginDispatch::class, ['expand.test']);

        $box->expand(
            $this->getPsrRequest(
                '',
                [
                    'Content-type' => 'text/html',
                    ProtocolExpand::HEADER__PREFIX . IExpandingBox::FIELD__EXPAND => 'test.status'
                ]
            ),
            $this->getPsrResponse()
        );
        $this->assertEquals('Ok', $box->getValue()['status']);
        $this->assertEquals(['test.status'], $box->getExpand());
    }

    public function testErrors()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test'
        ]);

        $this->createSnuffPlugin(PluginException::class, ['expand.test']);

        $box->expand(
            $this->getPsrRequest(
                '',
                [
                    'Content-type' => 'text/html',
                    ProtocolExpand::HEADER__PREFIX . IExpandingBox::FIELD__EXPAND => 'test.status'
                ]
            ),
            $this->getPsrResponse()
        );
        $this->assertArrayHasKey('errors', $box->getValue());
    }

    public function testExpand()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test',
            ExpandingBox::FIELD__ROOT => 'root'
        ]);

        $this->createSnuffPlugin(PluginBox::class, ['expand.test']);
        $this->createSnuffPlugin(PluginRoot::class, ['expand.root.test']);

        $box->expand(
            $this->getPsrRequest(
                '',
                [
                    'Content-type' => 'text/html',
                    ProtocolExpand::HEADER__PREFIX . IExpandingBox::FIELD__EXPAND => 'test.status'
                ]
            ),
            $this->getPsrResponse()
        );
        $this->assertNotEmpty($box->getValue());
        $this->assertEquals('is ok', $box->getValue()['test.box']);
        $this->assertEquals('is ok', $box->getValue()['test.root.box']);
    }

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
