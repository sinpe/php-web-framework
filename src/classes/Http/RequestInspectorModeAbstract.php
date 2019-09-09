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

/**
 * 检查mode基类
 * 
 * @author sinpe <wupinglong@huitongjt.com>
 */
abstract class RequestInspectorModeAbstract
{
   /**
    * @var RequestInspector
    */
    protected $inspector;

    /**
     * __construct
     *
     * @param RequestInspector $inspector
     */
    public function __construct(RequestInspector $inspector)
    {
        $this->inspector = $inspector;
    }

    /**
     * 关联检查
     *
     * @return static
     */
    protected function putRelCheck(callable $fn)
    {
        $this->inspector->putRelCheck($fn);

        return $this;
    }
    
}
