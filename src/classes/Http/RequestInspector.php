<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * 请求检查
 * 
 * @author sinpe <wupinglong@huitongjt.com>
 */
abstract class RequestInspector
{
    /**
     * 路由参数
     *
     * @var array
     */
    private $routeParams = [];

    /**
     * 模式
     *
     * @var Object|string
     */
    protected $mode;

    /**
     * 字段及检查顺序，可由子类覆盖
     *
     * @var array
     */
    protected $fields = [];

    /**
     * 预设值
     *
     * @var array
     */
    protected $datas = [];

    /**
     * 排序字段到真实字段映射，由子类实际指定
     *
     * @var array
     */
    protected $orderFieldMaps = [
        // 'table_field_name' => 'sort_name'
    ];

    /**
     * 表单字段到表字段映射，由子类实际指定
     *
     * @var array
     */
    protected $tableField2FormFields = [
        // 'table_field_name' => 'form_field_name'
    ];

    /**
     * @var callable[]
     */
    private $relCheckFn = [];

    /**
     * 关联检查
     *
     * @return void
     */
    final public function putRelCheck(callable $fn)
    {
        $this->relCheckFn[] = $fn;
        return $this;
    }

    /**
     * 设置模式
     *
     * @param Object|string $mode
     * @param array $fields 字段
     * @param array $datas 预设值
     * 
     * @return void
     */
    final public function setMode(
        $mode,
        array $fields = [],
        array $datas = []
    ) {
        $this->mode = $mode;
        $this->fields = $fields;
        $this->datas = $datas;
        return $this;
    }

    /**
     * 表字段映射到表单字段
     *
     * @param string $tableField
     * @param bool $inverse 逆向
     * @return string
     */
    final public function tableField2FormField(string $field, $inverse = false): string
    {
        if ($inverse) {
            $tableField = array_search($field, $this->tableField2FormFields, true);
            if ($tableField !== false) {
                return $tableField;
            }
        } else {
            if (isset($this->tableField2FormFields[$field])) {
                return $this->tableField2FormFields[$field];
            }
        }

        return $field;
    }

    /**
     * 严格
     *
     * @var boolean
     */
    private $strict = false;

    /**
     * 设置严格模式开启，控制字段的检查范围，比如在增加、修改操作时过滤未指定的字段的输入
     *
     * @param boolean $value
     * @return static
     */
    public function setStrict(bool $value)
    {
        $this->strict = $value;
        return $this;
    }

    /**
     * 执行检查
     *
     * @param ServerRequestInterface $request
     * @return \ArrayObject
     */
    final public function handle(ServerRequestInterface $request): \ArrayObject
    {
        // 绑定route参数
        foreach ($request->getAttribute('route')->getArguments() as $key => $value) {
            $this->routeParams[snake($key)] = $value; // 蛇型
            $this->routeParams[camel($key)] = $value; // 驼峰型
        }

        $params = $this->getParams($request);

        $handled = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

        if (!$this->strict) {
            $fields = array_merge($this->fields, array_diff(array_keys($params), $this->fields));
        } else {
            $fields = $this->fields;
        }

        // 指定特定模式做检查
        if ($this->mode) {
            if (is_string($this->mode)) {
                $studlyMode = studly($this->mode);
                $reflectionClass = new \ReflectionClass(static::class);
                $modeClass = $reflectionClass->getNamespaceName() . "\\{$studlyMode}Mode";
                if (class_exists($modeClass)) {
                    $mode = new $modeClass($this);
                }
            } else {
                $mode = $this->mode;
            }
        }

        // 根据指定的字段或者表单字段验证
        // 字段检查有先后，可以根据表单位置来调整，或者通过设置fields属性来调整这个顺序
        foreach ($fields as $field) {
            // 
            $value = $params[$field];
            // 检查各字段，有才检查，表单提交有或通过指定fields
            $handleMethod = 'handle' . studly($field);

            // 有独立的mode类，则忽略inspecter类中待mode的方法
            if (!$mode) {
                $modeHandleMethod = 'handle' . $studlyMode . studly($field);
                if (method_exists($this, $modeHandleMethod)) {
                    $callable = [$this, $modeHandleMethod];
                } elseif (method_exists($this, $handleMethod)) {
                    $callable = [$this, $handleMethod];
                } else {
                    $callable = null;
                }
            } else {
                // mode中做检查
                if (method_exists($mode, $handleMethod)) {
                    $callable = [$mode, $handleMethod];
                } elseif (method_exists($this, $handleMethod)) { // 主对象中做检查
                    $callable = [$this, $handleMethod];
                } else {
                    $callable = null;
                }
            }

            if ($callable) {
                // 
                $processed = call_user_func($callable, $value, $field, $handled); //$this->{$method}($value, $field, $handled);

                // 没有返回值的，放弃该项
                if (!is_null($processed)) {
                    // 返回generator函数，一般是返回多个key的值
                    if ($processed instanceof \Closure) {
                        $handled = new \ArrayObject(
                            array_merge((array) $handled, iterator_to_array($processed($handled))),
                            \ArrayObject::ARRAY_AS_PROPS
                        );
                    } else { // 返回普通的值，一般是保持单key值
                        $tableField = $this->tableField2FormField($field, true);
                        $handled[$tableField] = $processed;
                    }
                }
            } else {
                $tableField = $this->tableField2FormField($field, true);
                $handled[$tableField] = $params[$field];
            }
        }

        unset($params, $fields);

        // 关联检查
        if (!empty($this->relCheckFn)) {
            foreach ($this->relCheckFn as $fn) {
                $fn($handled);
            }
        }

        // 对检查通过的一并再处理，比如：再附加路由参数到整个数据集中
        if ($mode && method_exists($mode, 'process')) {
            $handled = $mode->process($handled);
        }

        // 预设值
        foreach ($this->datas as $key => $value) {

            $tableField = $this->tableField2FormField($key, true);
            if (!isset($handled[$tableField])) {
                $handled[$tableField] = $value;
            }
        }

        return $handled;
    }

    /**
     * 获取request参数
     *
     * 默认返回reqeust的所有参数，可按需在子类覆盖重写，做一定的过滤
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        $requestParams = $request->getParams();

        $normalized = [];

        foreach ($requestParams as $key => $value) {
            $normalized[snake($key)] = $value; // 蛇形串
        }

        return $normalized;
    }

    /**
     * __get
     * 
     * 快捷使用路由参数
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->routeParams[$key] ?? null;
    }
}
