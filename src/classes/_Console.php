<?php

namespace Sinpe\Framework;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Console extends Application
{
    /**
     * ContainerInterface
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        $container = $this->generateContainer();
        $container[SettingInterface::class] = $this->generateSetting();
        $this->container = $container;
        parent::__construct($name, $version);
        // 生命周期函数__init
        $this->__init();
    }

    /**
     * Get container
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * __init
     * 
     * 需要额外的初始化，覆盖此方法
     *
     * @return void
     */
    protected function __init()
    { }

    /**
     * create setting
     * 
     * 需要替换默认的setting，覆盖此方法
     *
     * @return SettingInterface
     */
    protected function generateSetting(): SettingInterface
    {
        $settings = require_once __DIR__  . '/../settings.php';

        return new Setting($settings);
    }

    /**
     * create container
     * 
     * 需要替换默认的container，覆盖此方法
     *
     * @return ContainerInterface
     */
    protected function generateContainer(): ContainerInterface
    {
        return new Container();
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws \Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    final public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // 生命周期函数__runBefore
        $output = $this->__runBefore($input, $output);

        return parent::run($input, $output);
    }

    /**
     * __runBefore
     *
     * @return OutputInterface
     */
    protected function __runBefore(InputInterface $input = null, OutputInterface $output = null): OutputInterface
    {
        return $output;
    }
}
