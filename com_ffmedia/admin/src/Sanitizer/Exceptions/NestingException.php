<?php
/**
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Sanitizer\Exceptions;

\defined('JPATH_PLATFORM') or die;

use Exception;

class NestingException extends \Exception
{
    /**
     * @var \DOMElement
     */
    protected $element;

    /**
     * NestingException constructor.
     *
     * @param string           $message
     * @param int              $code
     * @param Exception|null   $previous
     * @param \DOMElement|null $element
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, \DOMElement $element = null)
    {
        $this->element = $element;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the element that caused the exception.
     *
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }
}