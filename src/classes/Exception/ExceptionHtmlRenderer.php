<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Exception;

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\DataObject;
use Sinpe\Framework\RendererInterface;

/**
 * The HTML renderer for exception with debug details.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ExceptionHtmlRenderer implements RendererInterface
{
    /**
     * @var DataObject
     */
    private $option;

    /**
     * __construct
     *
     * @param DataObject $option
     */
    public function __construct(DataObject $option)
    {
        $this->option = $option;
    }

    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function process(DataObject $output)
    {
        $title = 'Fault';

        if ($this->option->displayErrorDetails) {

            $thrown = $output->thrown;

            $html = '<p>The application could not run because of the following error:</p>';
            $html .= '<h2>Details</h2>';
            $html .= $this->renderHtmlError($thrown);

            while ($thrown = $thrown->getPrevious()) {
                $html .= '<h2>Previous error</h2>';
                $html .= $this->renderHtmlError($thrown);
            }
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }

        return sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
                "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
                "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
                "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
            $title,
            $title,
            $html
        );

    }

    /**
     * Render exception or error as HTML.
     *
     * @return string
     */
    protected function renderHtmlError($thrown)
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($thrown));

        if (($code = $thrown->getCode())) {
            $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
        }

        if (($message = $thrown->getMessage())) {
            $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($message));
        }

        if (($file = $thrown->getFile())) {
            $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
        }

        if (($line = $thrown->getLine())) {
            $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
        }

        if (($trace = $thrown->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
        }

        return $html;
    }

}
