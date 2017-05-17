<?php
/**
 * @category   CLS
 * @package    Core
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Core_Coretests_ErrorController extends Mage_Core_Controller_Front_Action
{
    /**
     * Test out the default error handling process (useful in dev environments where errors are usually displayed directly)
     *
     * @throws Exception
     */
    public function indexAction() {
        throw new Exception("Testing the error handler.");
    }
}
