<?php


namespace yun\factory;


use yun\exception\InvalidConfigException;
use yun\exception\NotInstantiableException;
use yun\helpers\LangHelper;

class Container
{

    /**
     * @var array 保存所有单例模式的对象实例
     */
    private $singletons = [];

    /**
     * @var array 保存所有对象构造函数所需的变量
     */
    private $definitions = [];

    /**
     * @var array 保存所有对象的参数
     * 这些参数在对象实例化以后作为对象的属性传入
     */
    private $params = [];

    /**
     * @var array 保存类的反射实例
     */
    private $reflections = [];

    /**
     * @var array 保存类的依赖数组,即构造函数参数
     */
    private $dependencies = [];

    /**
     * 设置指定类的实例化方式,即设置指定类实例化所用的构造参数和属性
     * @param $class 类名
     * @param array $definition 构造参数数组
     * @param array $params 实例化后作为类的属性
     * @return $this 返回工程实例,可以连续调用
     * @author hyunsu
     * @time 2019-06-06 12:19
     */
    public function set($class, $definition = [], array $params = [])
    {
        $this->$definitions[$class] = $definition;
        $this->params[$class] = $params;
        unset($this->singletons[$class]);
        return $this;
    }

    public function setSingleton($class, $definition = [], array $params = [])
    {
        $this->$definitions[$class] = $definition;
        $this->params[$class] = $params;
        $this->singletons[$class] = null;
        return $this;
    }

    /**
     * 获取一个类的实例
     * @param $class 类名
     * @param array $definitions 构造参数数组
     * @param array $params 实例化后作为类的属性
     * @return mixed
     * @throws NotInstantiableException
     * @author hyunsu
     * @time 2019-06-06 12:20
     */
    public function get($class, $definitions = [], $params = [])
    {
        if (isset($this->singletons[$class])) {
            //单例中存在,直接返回
            if ($this->singletons[$class] == null) {
                $this->singletons[$class] = $this->get($class, $definitions, $params);
            }
            return $this->singletons[$class];
        }

        if (isset($definition[$class])) {
            //用户使用 set 设置的
            return $this->build($class, $this->definitions[$class], $this->params[$class]);
        }

        //用户没设置过,也不是单例,直接创建一个新的
        return $this->build($class, $definitions, $params);
    }

    /**
     * 创建指定类的实例。
     * 此方法将解析指定类的依赖项，实例化它们，然后注入
     * 将自定义它们放入指定类的新实例中。
     * @param $class 类的名称
     * @param $definitions 自定义构造函数参数数组
     * @param $params 自定义属性
     * @return mixed
     * @throws NotInstantiableException
     * @author hyunsu
     * @time 2019-06-06 12:09
     */
    protected function build($class, $definitions, $params)
    {
        /* @var $reflection ReflectionClass */
        list($reflection, $dependencies) = $this->getDependencies($class);

        //整合解析出的依赖参数和自定义构造参数
        foreach ($definitions as $index => $def) {
            $dependencies[$index] = $def;
        }

        $dependencies = $this->resolveDependencies($dependencies, $reflection);
        // isInstantiable() 方法判断类是否可以实例化
        if (!$reflection->isInstantiable()) {
            throw new NotInstantiableException($reflection->name);
        }

        //通过反射实例化类
        $object = $reflection->newInstanceArgs($dependencies);

        if (!empty($params)) {
            $params = $this->resolveDependencies($params);

            //将参数作为属性传入
            foreach ($params as $name => $value) {
                $object->$name = $value;
            }

        }
        return $object;
    }

    /**
     * 获取某个类的依赖,即获取类的构造函数参数
     * 通过反射获取类的构造函数,如果不存在,则直接加入$reflections数组,依赖为空数组加入$dependencies
     * 否则遍历所有参数 按照顺序加入 $dependencies[$class] 数组中
     * 遍历时候如果有默认值,直接取默认值,否则作为 yun\factory\Instance的实例
     * @param $class 类名
     * @return array 返回类的反射和依赖数组
     * @author hyunsu
     * @time 2019-06-06 11:42
     */
    protected function getDependencies($class)
    {
        //如果反射实例数组中存在,则直接返回这个实例和依赖
        if (isset($this->reflections[$class])) {
            return [$this->reflections[$class], $this->dependencies[$class]];
        }

        $dependencies = [];
        //尝试反射对象
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new InvalidConfigException(null, LangHelper::ts('Failed to instantiate component or class : `%s`', $class), 0, $e);
        }
        //获取构造函数
        $constructor = $reflection->getConstructor();

        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $param) {
                if (version_compare(PHP_VERSION, '5.6.0', '>=') && $param->isVariadic()) {
                    break;
                } elseif ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    $c = $param->getClass();
                    $dependencies[] = Instance::of($c === null ? null : $c->getName());
                }
            }
        }

        $this->reflections[$class] = $reflection;
        $this->dependencies[$class] = $dependencies;

        return [$reflection, $dependencies];
    }


    /**
     * 实例化指定类的依赖类
     * 在 [[getDependencies()]] 方法中,将所有没有默认值的参数全部作为了Instance的实例
     * 该方法循环解析这些实例,如果id不是null,也就是确实是某个类,则进行实例化
     * @param $dependencies 指定类的依赖数组
     * @return mixed
     * @author hyunsu
     * @time 2019-06-06 11:52
     */
    protected function resolveDependencies($dependencies)
    {
        foreach ($dependencies as $index => $dependency) {
            if ($dependency instanceof Instance) {
                if ($dependency->id !== null) {
                    $dependencies[$index] = $this->get($dependency->id);
                }
            }
        }

        return $dependencies;
    }
}