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

//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

/**
 * Class Categories
 * @package XoopsModules\Myservices
 */
class Categories extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('categories_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('categories_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('categories_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('categories_imgurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('categories_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('categories_advertisement', XOBJ_DTYPE_TXTAREA, null, false);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Returns the link to use to go to a category according to the module's options (with or without URL rewriting)
     * @return string the html link to go to the category
     */
    public function getCategoryLink()
    {
        $cat_cid   = $this->getVar('categories_id');
        $cat_title = $this->getVar('categories_title', 'n');
        $url       = '';

        if (1 ==\XoopsModules\Myservices\Utilities::getModuleOption('urlrewriting')) {    // On utilise l'url rewriting
            $url = MYSERVICES_URL . 'category-' . (int)$cat_cid .\XoopsModules\Myservices\Utilities::makeSeoUrl($cat_title) . '.html';
        } else {    // Pas d'utilisation de l'url rewriting
            $url = MYSERVICES_URL . 'category.php?categories_id=' . (int)$cat_cid;
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
        $ret['categories_href_title'] =\XoopsModules\Myservices\Utilities::makeHrefTitle($this->getVar('categories_title'));
        $ret['categories_url']        = $this->getCategoryLink();

        // URL complète de l'image
        if ('' != xoops_trim($this->getVar('categories_imgurl'))) {
            $ret['categories_fullimgurl'] = XOOPS_UPLOAD_URL . '/' . $this->getVar('categories_imgurl');
        } else {
            $ret['categories_fullimgurl'] = MYSERVICES_IMAGES_URL . 'blank.gif';
        }

        return $ret;
    }
}
