<?php
/**
 * @category   CLS
 * @package    Core
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Core_Helper_Variable extends Mage_Core_Helper_Abstract
{

    protected $_variablesByCode = array();

    /**
     * Set the specified value for the variable code.
     *
     * @param string $code
     * @param string $value
     * @return CLS_Core_Helper_Variable
     */
    public function setVariable($code, $value) {
        $variable = $this->_getVariableInstance($code);
        $variable->setPlainValue((string) $value)
            ->setUserVisible(false) //hide from admin
            ->save();
        return $this;
    }

    /**
     * Retrieve the value for the specified variable code.
     *
     * @param string $code
     * @return string | false
     */
    public function getVariable($code) {
        $variable = $this->_getVariableInstance($code);
        return ($variable->getId()) ? $variable->getValue(Mage_Core_Model_Variable::TYPE_TEXT) : false;
    }

    /**
     * Deletes the variable for the supplied code.
     *
     * @param string $code
     * @return CLS_Core_Helper_Variable
     */
    public function deleteVariable($code) {
        $variable = $this->_getVariableInstance($code);
        if ($variable->getId()) {
            $variable->delete();
        }
        return $this;
    }

    /**
     * Retrieve the appropriate variable from our cache array if present. If not present, pull it from the database (if it already has a record) and set that instance in our cache array.
     *
     * @param string $code
     * @return Mage_Core_Model_Variable
     */
    protected function _getVariableInstance($code) {
        //The code isn't allowed to have characters like spaces in it. Let's convert it the same way we would a url param just to be safe.
        $code = Mage::getSingleton('catalog/product_url')->formatUrlKey($code);
        //If we haven't yet defined a variable instance for this code, attempt to pull it from the db and set it in the array.
        if (!isset($this->_variablesByCode[$code])) {
            $variable = Mage::getModel('core/variable')->loadByCode($code);
            $variable->setCode($code)
                ->setName($code)
                ->setHtmlValue('');
            $this->_variablesByCode[$code] = $variable;
        }
        return $this->_variablesByCode[$code];
    }

}