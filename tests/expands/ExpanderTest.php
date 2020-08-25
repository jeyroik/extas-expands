<?php
namespace tests\expands;

use extas\components\expands\Box;
use extas\components\expands\Expand;
use extas\components\http\TSnuffHttp;
use extas\components\Item;
use extas\components\plugins\expands\PluginExpand;
use extas\components\plugins\expands\PluginParseSkipEmpty;
use extas\components\plugins\expands\PluginParseWildcard;
use extas\components\plugins\Plugin;
use extas\components\plugins\TSnuffPlugins;
use extas\components\protocols\ProtocolExpand;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use tests\expands\misc\ExpandTestIsOk;
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
    use THasMagicClass;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->createSnuffDynamicRepositories([
            ['expandBoxes', 'name', Box::class]
        ]);
        $this->getMagicClass('expandBoxes')->create(new Box([
            'name' => 'test.is_ok',
            'aliases' => ['test.is_ok', 'test']
        ]));
        $this->getMagicClass('expandBoxes')->create(new Box([
            'name' => 'test.failed',
            'aliases' => ['test.failed', 'test']
        ]));
        $this->getMagicClass('expandBoxes')->create(new Box([
            'name' => 'test.work',
            'aliases' => ['test.work', 'test']
        ]));
        $this->createWithSnuffRepo('pluginRepository', new Plugin([
            Plugin::FIELD__CLASS => PluginExpand::class,
            Plugin::FIELD__STAGE => 'extas.expand.@expand'
        ]));
        $this->createWithSnuffRepo('pluginRepository', new Plugin([
            Plugin::FIELD__CLASS => PluginParseWildcard::class,
            Plugin::FIELD__STAGE => 'extas.expand.parse'
        ]));
        $this->createWithSnuffRepo('pluginRepository', new Plugin([
            Plugin::FIELD__CLASS => PluginParseSkipEmpty::class,
            Plugin::FIELD__STAGE => 'extas.expand.parse'
        ]));
        $this->createWithSnuffRepo('pluginRepository', new Plugin([
            Plugin::FIELD__CLASS => ExpandTestIsOk::class,
            Plugin::FIELD__STAGE => 'extas.expand.test.is_ok'
        ]));
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffPlugins();
        $this->deleteSnuffDynamicRepositories();
    }

    public function testExpand()
    {
        $item = new class() extends Item {
            protected function getSubjectForExtension(): string
            {
                return 'test';
            }
        };

        $expand = new class ([
            Expand::FIELD__PSR_REQUEST => $this->getPsrRequest(),
            Expand::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            Expand::FIELD__ARGUMENTS => [
                'expand' => ' Test.is_oK , teSt.* ,  '
            ]
        ]) extends Expand {
            protected array $expands = [];

            public function getParsedExpands()
            {
                return $this->expands;
            }

            protected function parseExpand(): array
            {
                $this->expands = parent::parseExpand();

                return $this->expands;
            }
        };

        $item = $expand->expand($item);

        $this->assertEquals(
            [
                'expand' => ['test.is_ok', 'test.failed', 'test.work'],
                'test' => 'is ok'
            ],
            $item->__toArray()
        );

        $need = ['test.is_ok', 'test.failed', 'test.work', '@expand'];
        $current = $expand->getParsedExpands();

        $this->assertCount(
            4,
            $current,
            'Incorrect expands count: ' . print_r($current, true)
        );

        foreach ($need as $item) {
            $this->assertTrue(
                in_array($item, $current),
                'Missed expand "' . $item . '" in ' . print_r($current, true)
            );
        }
    }

    public function testExpandProtocol()
    {
        $protocol = new ProtocolExpand();
        $args = [];

        $protocol($args, $this->getPsrRequest('', [
            ProtocolExpand::HEADER__PREFIX . 'expand' => 'test.is_ok'
        ]));

        $this->assertEquals(
            [Expand::ARG__EXPAND => 'test.is_ok'],
            $args,
            'Missed or incorrect expand: ' . print_r($args, true)
        );
    }
}
