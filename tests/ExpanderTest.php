<?php
namespace tests;

use extas\components\expands\Expander;
use extas\components\expands\ExpandingBox;
use extas\components\expands\ExpandRequired;
use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\components\protocols\ProtocolExpand;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\repositories\IRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;
use Slim\Http\Uri;

/**
 * Class ExpanderTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class ExpanderTest extends TestCase
{
    protected IRepository $pluginRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->pluginRepo = new class extends PluginRepository {
            public function reload()
            {
                parent::$stagesWithPlugins = [];
            }
        };
    }

    protected function tearDown(): void
    {
        $this->pluginRepo->reload();
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => [
            PluginBox::class,
            PluginRoot::class,
            PluginDispatch::class,
            PluginException::class
        ]]);
    }

    public function testExpandDispatching()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test'
        ]);

        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginDispatch::class,
            Plugin::FIELD__STAGE => 'expand.test'
        ]));

        $box->expand($this->getRequest(), $this->getResponse());
        $this->assertEquals('Ok', $box->getValue()['status']);
        $this->assertEquals(['test.status'], $box->getExpand());
    }

    public function testErrors()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test'
        ]);

        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginException::class,
            Plugin::FIELD__STAGE => 'expand.test'
        ]));

        $box->expand($this->getRequest(), $this->getResponse());
        $this->assertArrayHasKey('errors', $box->getValue());
    }

    public function testExpand()
    {
        $box = new ExpandingBox([
            ExpandingBox::FIELD__NAME => 'test',
            ExpandingBox::FIELD__ROOT => 'root'
        ]);

        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginBox::class,
            Plugin::FIELD__STAGE => 'expand.test'
        ]));

        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginRoot::class,
            Plugin::FIELD__STAGE => 'expand.root.test'
        ]));

        $box->expand($this->getRequest(), $this->getResponse());
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

    /**
     * @return RequestInterface
     */
    protected function getRequest(): RequestInterface
    {
        return new Request(
            'GET',
            new Uri('http', 'localhost', 80, '/'),
            new Headers([
                'Content-type' => 'text/html',
                ProtocolExpand::HEADER__PREFIX . IExpandingBox::FIELD__EXPAND => 'test.status'
            ]),
            [],
            [],
            new Stream(fopen('php://input', 'r'))
        );
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponse(): ResponseInterface
    {
        return new Response();
    }
}
