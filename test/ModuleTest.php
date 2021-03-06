<?php

/**
 * @see       https://github.com/laminas/laminas-zendframework-bridge for the canonical source repository
 * @copyright https://github.com/laminas/laminas-zendframework-bridge/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-zendframework-bridge/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ZendFrameworkBridge;

use Laminas\ZendFrameworkBridge\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testInitRegistersListenerWithEventManager()
    {
        $eventManager = new TestAsset\EventManager();
        $moduleManager = new TestAsset\ModuleManager($eventManager);
        $module = new Module();

        $module->init($moduleManager);

        $this->assertSame(
            array('mergeConfig' => array(array($module, 'onMergeConfig'))),
            $eventManager->getListeners()
        );
    }

    /**
     * @return iterable
     */
    public function configurations()
    {
        return array(
            array('Acelaya Expressive Slim Router', 'ExpressiveSlimRouterConfig.php'),
            array('mwop.net App module config', 'MwopNetAppConfig.php')
        );
    }

    /**
     * @dataProvider configurations
     * @param string $name
     * @param string $configFile
     */
    public function testOnMergeConfigProcessesAndReplacesConfigurationPulledFromListener($name, $configFile)
    {
        $configFile = sprintf('%s/TestAsset/ConfigPostProcessor/%s', __DIR__, $configFile);
        $expectationsFile = $configFile . '.out';
        $config = require $configFile;
        $expected = require $expectationsFile;

        $listener = new TestAsset\ConfigListener($config);
        $event = new TestAsset\ModuleEvent($listener);
        $module = new Module();

        $this->assertNull($module->onMergeConfig($event));

        $this->assertSame($expected, $listener->getMergedConfig(), $name);
    }
}
