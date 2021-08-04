<?php

namespace YOU_PLUGIN;

use Pimple\Container;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use YOU_PLUGIN\Activate\ActivateServiceProvider;
use YOU_PLUGIN\I18n\I18nServiceProvider;
use YOU_PLUGIN\Processors\Actions;
use YOU_PLUGIN\Processors\Filters;
use YOU_PLUGIN\Support\Facade;

/**
 * Class Application
 *
 * @package YOU_PLUGIN
 */
class Application extends Container
{
    protected array $providers = [
        ActivateServiceProvider::class,
        I18nServiceProvider::class,
    ];

    public array $processors = [
        Actions::class,
        Filters::class,
    ];

    public function bootstrap()
    {
        $this->define_tables();
        $this->registerProviders();
        Facade::setFacadeApplication($this);
        $this->processes();
        return $this;
    }

    public function registerProviders()
    {
        foreach (apply_filters('YOU_PLUGIN_providers', $this->providers) as $provider) {
            $this->register(new $provider());
        }
    }

    public function processes()
    {
        foreach (apply_filters('YOU_PLUGIN_processors', $this->processors) as $processor) {
            $processor = $this->make($processor);
            $processor->process($this);
        }
    }

    /**
     * @param string $class_name
     * @return string
     */
    public function normalize_name(string $class_name)
    {
        return ltrim($class_name, '\\');
    }

    /**
     * 从容器中获取对象
     *
     * @param string $name
     * @param array $args
     * @return object
     */
    public function make(string $name, array $args = [])
    {
        $name = $this->normalize_name($name);

        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }

        return $this->provision_instance($name, $args);
    }

    /**
     * 实例化一个对象
     *
     * @param string $class_name
     * @param array $args
     * @return object
     * @throws ReflectionException
     */
    private function provision_instance(string $class_name, array $args = [])
    {
        $reflection_class = new ReflectionClass($class_name);
        $constructor = $reflection_class->getConstructor();

        if (!$constructor) {
            if (!$reflection_class->isInstantiable()) {
                $type = $reflection_class->isInterface() ? 'interface' : 'abstract class';
                throw new ProvisionException('The %s "%s" is not defined.', $type, $class_name);
            }
            $obj = new $class_name();
        } elseif (!$constructor->isPublic()) {
            throw new ProvisionException(sprintf('Cannot instantiate protected/private constructor in class %s', $class_name));
        } else {
            $args = $this->provision_func_args($constructor, $args);
            $obj = $reflection_class->newInstanceArgs($args);
        }

        return $obj;
    }

    /**
     * 获取要实例对象的参数
     *
     * @param ReflectionFunctionAbstract $func
     * @param array $definition
     * @return array
     */
    private function provision_func_args(ReflectionFunctionAbstract $func, array $definition)
    {
        $args = [];

        $params = $func->getParameters();

        foreach ($params as $i => $param) {
            // 如果definition有传入参数，使用definition的参数
            if (isset($definition[$i])) {
                $args[] = $definition[$i];
                continue;
            }

            if ($this->offsetExists($param->name)) {
                $args[] = $this->offsetGet($param->name);
                continue;
            }

            if ($type = $param->getType()) {
                $args[] = $this->make($type->getName());
            }
        }

        return $args;
    }

    public function define_tables()
    {
        global $wpdb;
        $tables = [
            'YOU_PLUGIN_tokens' => 'YOU_PLUGIN_tokens',
        ];

        foreach ($tables as $name => $table) {
            $wpdb->$name = $wpdb->prefix . $table;
        }
    }
}