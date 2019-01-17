<?php namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * A class used to manage currency
 *
 * @package       myservices
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 * @todo          Replace with a package like the Zend Framework Currency (http://framework.zend.com/manual/fr/zend.currency.html)
 *
 * Note: don't call it staticly or be sure to call the constructor
 *
 */
class Currency
{
    protected $_decimalsCount;
    protected $_thousandsSep;
    protected $_decimalSep;
    protected $_moneyFull;
    protected $_moneyShort;
    protected $_monnaiePlace;

    public function __construct()
    {
        // Get the module's preferences
        $this->_decimalsCount =\XoopsModules\Myservices\Utilities::getModuleOption('decimals_count');
        $this->_thousandsSep  =\XoopsModules\Myservices\Utilities::getModuleOption('thousands_sep');
        $this->_decimalSep    =\XoopsModules\Myservices\Utilities::getModuleOption('decimal_sep');
        $this->_moneyFull     =\XoopsModules\Myservices\Utilities::getModuleOption('money_full');
        $this->_moneyShort    =\XoopsModules\Myservices\Utilities::getModuleOption('money_short');
        $this->_monnaiePlace  =\XoopsModules\Myservices\Utilities::getModuleOption('monnaie_place');
    }

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Returns an amount according to the currency's preferences (defined in the module's options)
     *
     * @param float|int $amount The amount to work on
     * @return string The amount formated according to the currency
     */
    public function amountInCurrency($amount = 0)
    {
        return number_format($amount, $this->_decimalsCount, $this->_decimalSep, $this->_thousandsSep);
    }

    /**
     * Format an amount for display according to module's preferences
     *
     * @param  float|string  $amount The amount to format
     * @param  string $format Format to use, 's' for Short and 'l' for Long
     * @return string The amount formatted
     */
    public function amountForDisplay($amount, $format = 's')
    {
        $amount = $this->amountInCurrency($amount);

        $monnaieLeft = $monnaieRight = $monnaieSleft = $monnaieSright = '';
        if (1 == $this->_monnaiePlace) {    // To the right
            $monnaieRight  = ' ' . $this->_moneyFull;        // Long version
            $monnaieSright = ' ' . $this->_moneyShort;    // Short version
        } else {    // To the left
            $monnaieLeft  = $this->_moneyFull . ' ';    // Long version
            $monnaieSleft = $this->_moneyShort . ' ';    // Short version
        }
        if ('s' !== $format) {
            return $monnaieLeft . $amount . $monnaieRight;
        } else {
            return $monnaieSleft . $amount . $monnaieSright;
        }
    }
}
