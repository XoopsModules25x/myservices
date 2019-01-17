<?php

namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

/**
 * Class Employees
 * @package XoopsModules\Myservices
 */
class Employees extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('employees_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('employees_firstname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_lastname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_bio', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('employees_photo1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_photo2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_photo3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_photo4', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_photo5', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('employees_isactive', XOBJ_DTYPE_INT, null, false);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Returns the current employee's fullname
     */
    public function getEmployeeFullName()
    {
        return $this->getVar('employees_lastname') . ' ' . $this->getVar('employees_firstname');
    }

    /**
     * Returns the link to use to go to an employee according to the module's options (with or without URL rewriting)
     * @return string the html link to go to the category
     */
    public function getEmployeeLink()
    {
        $employee_id       = $this->getVar('employees_id');
        $employee_fullname = $this->getEmployeeFullName();
        $url               = '';

        if (1 == \XoopsModules\Myservices\Utilities::getModuleOption('urlrewriting')) {    // On utilise l'url rewriting
            $url = MYSERVICES_URL . 'employee-' . (int)$employee_id . \XoopsModules\Myservices\Utilities::makeSeoUrl($employee_fullname) . '.html';
        } else {    // Pas d'utilisation de l'url rewriting
            $url = MYSERVICES_URL . 'employee.php?employees_id=' . (int)$employee_id;
        }

        return $url;
    }

    /**
     * Returns data formated
     *
     * @param string $format Format de retour des données (en accord avec les paramètres de la fonction getVar() de XoopsObject)
     * @return array  Formated datas
     */
    public function toArray($format = 's')
    {
        $ret = [];
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }
        $ret['employees_href_title'] = \XoopsModules\Myservices\Utilities::makeHrefTitle($this->getEmployeeFullName());
        $ret['employees_fullname']   = $this->getEmployeeFullName();
        $ret['employees_link']       = $this->getEmployeeLink();
        for ($i = 1; $i <= 4; ++$i) {
            if ('' != xoops_trim($this->getVar('employees_photo' . $i))) {
                $ret['employees_photo' . $i . 'url'] = XOOPS_UPLOAD_URL . '/' . $this->getVar('employees_photo' . $i);
            } else {
                $ret['employees_photo' . $i . 'url'] = MYSERVICES_IMAGES_URL . 'blank.gif';
            }
        }

        return $ret;
    }
}
