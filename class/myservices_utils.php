<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * A set of useful and common functions
 *
 * @package       myservices
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 *
 * Note: You should be able to use it without the need to instanciate it.
 *
 */
class myservices_utils
{
    const MODULE_NAME = 'myservices';

    /**
     * Access the only instance of this class
     *
     * @return  object
     *
     * @static
     * @staticvar   object
     */
    public function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param string $option module option's name
     * @return mixed option's value
     */
    public static function getModuleOption($option)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = self::MODULE_NAME;
        static $tbloptions = [];
        if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
            return $tbloptions[$option];
        }

        $retval = false;
        if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
            if (isset($xoopsModuleConfig[$option])) {
                $retval = $xoopsModuleConfig[$option];
            }
        } else {
            $module_handler = xoops_getHandler('module');
            $module         =& $module_handler->getByDirname($repmodule);
            $config_handler = xoops_getHandler('config');
            if ($module) {
                $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $tbloptions[$option] = $retval;

        return $retval;
    }

    /**
     * Is Xoops 2.2.x ?
     *
     * @return boolean need to say it ?
     */
    public static function isX22()
    {
        $x22 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ('2' == substr($xv, 2, 1)) {
            $x22 = true;
        }

        return $x22;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return boolean need to say it ?
     */
    public static function isX23()
    {
        $x23 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ((int)substr($xv, 2, 1) >= 3) {
            $x23 = true;
        }

        return $x23;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param string $caption Caption to give to the editor
     * @param string $name    Editor's name
     * @param string $value   Editor's value
     * @param string $width   Editor's width
     * @param string $height  Editor's height
     * @param string $supplemental
     * @return object The editor to use
     */
    public static function getWysiwygForm($caption, $name, $value = '', $width = '100%', $height = '400px', $supplemental = '')
    {
        $editor                   = false;
        $x22                      = static::isX22() || self::isX23();
        $editor_configs           = [];
        $editor_configs['name']   = $name;
        $editor_configs['value']  = $value;
        $editor_configs['rows']   = 35;
        $editor_configs['cols']   = 60;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';

        $editor_option = static::getModuleOption('form_options');

        switch (strtolower($editor_option)) {
            case 'spaw':
                if (!$x22) {
                    if (is_readable(XOOPS_ROOT_PATH . '/class/spaw/formspaw.php')) {
                        require_once(XOOPS_ROOT_PATH . '/class/spaw/formspaw.php');
                        $editor = new XoopsFormSpaw($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'spaw', $editor_configs);
                }
                break;

            case 'fck':
                if (!$x22) {
                    if (is_readable(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php')) {
                        require_once(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php');
                        $editor = new XoopsFormFckeditor($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'fckeditor', $editor_configs);
                }
                break;

            case 'htmlarea':
                if (!$x22) {
                    if (is_readable(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php')) {
                        require_once(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php');
                        $editor = new XoopsFormHtmlarea($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'htmlarea', $editor_configs);
                }
                break;

            case 'dhtml':
                if (!$x22) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 10, 50, $supplemental);
                } else {
                    $editor = new XoopsFormEditor($caption, 'dhtmltextarea', $editor_configs);
                }
                break;

            case 'textarea':
                $editor = new XoopsFormTextArea($caption, $name, $value);
                break;

            case 'tinyeditor':
                if (is_readable(XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php')) {
                    require_once XOOPS_ROOT_PATH . '/class/xoopseditor/tinyeditor/formtinyeditortextarea.php';
                    $editor = new XoopsFormTinyeditorTextArea(['caption' => $caption, 'name' => $name, 'value' => $value, 'width' => '100%', 'height' => '400px']);
                }
                break;

            case 'koivi':
                if (!$x22) {
                    if (is_readable(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php')) {
                        require_once(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php');
                        $editor = new XoopsFormWysiwygTextArea($caption, $name, $value, '100%', '250px', '');
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'koivi', $editor_configs);
                }
                break;
        }

        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param string  $message Message to display
     * @param boolean $form    Is this a confirmation for a form ?
     * @return string the javascript code to insert in the link (or in the form)
     */
    public static function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        } else {
            return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        }
    }

    /**
     * Get current user IP
     *
     * @return string IP address (format Ipv4)
     */
    public static function IP()
    {
        $proxy_ip = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxy_ip = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        $regs = [];
        if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}\.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0) {
            $the_IP = $regs[0];
        } else {
            $the_IP = $_SERVER['REMOTE_ADDR'];
        }

        return $the_IP;
    }

    /**
     * Set the page's title, meta description and meta keywords
     * Datas are supposed to be sanitized
     *
     * @param string $pageTitle       Page's Title
     * @param string $metaDescription Page's meta description
     * @param string $metaKeywords    Page's meta keywords
     * @return void
     */
    public static function setMetas($pageTitle = '', $metaDescription = '', $metaKeywords = '')
    {
        global $xoTheme, $xoTheme, $xoopsTpl;
        $xoopsTpl->assign('xoops_pagetitle', $pageTitle);
        if (isset($xoTheme) && is_object($xoTheme)) {
            if (!empty($metaKeywords)) {
                $xoTheme->addMeta('meta', 'keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoTheme->addMeta('meta', 'description', $metaDescription);
            }
        } elseif (isset($xoopsTpl) && is_object($xoopsTpl)) {    // Compatibility for old Xoops versions
            if (!empty($metaKeywords)) {
                $xoopsTpl->assign('xoops_meta_keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoopsTpl->assign('xoops_meta_description', $metaDescription);
            }
        }
    }

    /**
     * Send an email from a template to a list of recipients
     *
     * @param        $tplName
     * @param array  $recipients List of recipients
     * @param string $subject    Email's subject
     * @param array  $variables  Varirables to give to the template
     * @return bool Result of the send
     * @internal param string $tpl_name Template's name
     */
    public static function sendEmailFromTpl($tplName, $recipients, $subject, $variables)
    {
        global $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
        if (!is_array($recipients)) {
            if ('' == trim($recipients)) {
                return false;
            }
        } else {
            if (0 == count($recipients)) {
                return false;
            }
        }
        if (function_exists('xoops_getMailer')) {
            $xoopsMailer = xoops_getMailer();
        } else {
            $xoopsMailer =& getMailer();
        }

        $xoopsMailer->useMail();
        $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . self::MODULE_NAME . '/language/' . $xoopsConfig['language'] . '/mail_template');
        $xoopsMailer->setTemplate($tplName);
        $xoopsMailer->setToEmails($recipients);
        // TODO: Change !
        // $xoopsMailer->setFromEmail('contact@monsite.com');
        //$xoopsMailer->setFromName('MonSite');
        $xoopsMailer->setSubject($subject);
        foreach ($variables as $key => $value) {
            $xoopsMailer->assign($key, $value);
        }
        $res = $xoopsMailer->send();
        unset($xoopsMailer);

        $fp = @fopen(XOOPS_UPLOAD_PATH . '/logmail_' . self::MODULE_NAME . '.txt', 'a');
        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            fwrite($fp, 'Template name : ' . $tplName . "\n");
            fwrite($fp, 'Email subject : ' . $subject . "\n");
            if (is_array($recipients)) {
                fwrite($fp, 'Recipient(s) : ' . implode(',', $recipients) . "\n");
            } else {
                fwrite($fp, 'Recipient(s) : ' . $recipients . "\n");
            }
            fwrite($fp, 'Transmited variables : ' . implode(',', $variables) . "\n");
            fclose($fp);
        }

        return $res;
    }

    /**
     * Remove module's cache
     */
    public static function updateCache()
    {
        global $xoopsModule;
        $folder  = $xoopsModule->getVar('dirname');
        $tpllist = [];
        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $tplfile_handler = xoops_getHandler('tplfile');
        $tpllist         = $tplfile_handler->find(null, null, null, $folder);
        xoops_template_clear_module_cache($xoopsModule->getVar('mid'));            // Clear module's blocks cache

        foreach ($tpllist as $onetemplate) {    // Remove cache for each page.
            if ('module' === $onetemplate->getVar('tpl_type')) {
                //  Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
                $files_del = [];
                $files_del = glob(XOOPS_CACHE_PATH . '/*' . $onetemplate->getVar('tpl_file') . '*');
                if (count($files_del) > 0 && is_array($files_del)) {
                    foreach ($files_del as $one_file) {
                        if (is_file($one_file)) {
                            unlink($one_file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param        integer  timeout Time to wait before to redirect
     */
    public static function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return object The module
     */
    protected static function _getModule()
    {
        static $mymodule;
        if (!isset($mymodule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && MYSERVICES_DIRNAME == $xoopsModule->getVar('dirname')) {
                $mymodule =& $xoopsModule;
            } else {
                $hModule  = xoops_getHandler('module');
                $mymodule = $hModule->getByDirname(MYSERVICES_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     * @return string Module's name
     */
    public static function getModuleName()
    {
        static $moduleName;
        if (!isset($moduleName)) {
            $mymodule   = static::_getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * Create a title for the href tags inside html links
     *
     * @param string $title Text to use
     * @return string Formated text
     */
    public static function makeHrefTitle($title)
    {
        $s = "\"'";
        $r = '  ';

        return strtr($title, $s, $r);
    }

    /**
     * Returns the list of the users of a group
     *
     * @param int $groupId Searched group
     * @return array Array of XoopsUsers
     */
    public static function getUsersFromGroup($groupId)
    {
        $tblUsers       = [];
        $member_handler = xoops_getHandler('member');
        $tblUsers       = $member_handler->getUsersByGroup($groupId, true);

        return $tblUsers;
    }

    /**
     * Returns the list of emails of a group
     *
     * @param $groupId
     * @return array Emails list
     * @internal param int $group_id Group's number
     */
    public static function getEmailsFromGroup($groupId)
    {
        $ret      = [];
        $tblUsers = static::getUsersFromGroup($groupId);
        foreach ($tblUsers as $user) {
            $ret[] = $user->getVar('email');
        }

        return $ret;
    }

    /**
     * Verify that the current user is a member of the Admin group
     *
     * @return booleean Admin or not
     */
    public static function isAdmin()
    {
        global $xoopsUser, $xoopsModule;
        if (is_object($xoopsUser)) {
            if (in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
                return true;
            } elseif (isset($xoopsModule) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the current date in the Mysql format
     *
     * @return string Date in the Mysql format
     */
    public static function getCurrentSQLDate()
    {
        return date('Y-m-d');    // 2007-05-02
    }

    /**
     * Convert a Mysql date to the human's format
     *
     * @param string $dateTime The date/time to convert
     * @param string $format
     * @return string The date in a human form
     */
    public static function SQLDateToHuman($dateTime, $format = 'l')
    {
        return formatTimestamp(strtotime($dateTime), $format);
    }

    /**
     * Convert a timestamp to a Mysql date
     *
     * @param integer $timestamp The timestamp to use
     * @return string The date in the Mysql format
     */
    public static function timestampToMysqlDate($timestamp)
    {
        return date('Y-m-d', $timestamp);
    }

    /**
     * Conversion d'un dateTime Mysql en date lisible en français
     * @param $dateTime
     * @return bool|string
     */
    public static function sqlDateTimeToFrench($dateTime)
    {
        return date('d/m/Y H:i:s', strtotime($dateTime));
    }

    /**
     * Convert a timestamp to a Mysql datetime form
     * @param integer $timestamp The timestamp to use
     * @return string The date and time in the Mysql format
     */
    public static function timestampToMysqlDateTime($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return boolean Yes = we need to add them, false = no
     */
    public static function needsAsterisk()
    {
        return false;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param object $sform The form to modify
     * @return object The modified form
     * @internal param string $caracter The character to use to mark fields
     */
    public static function formMarkRequiredFields($sform)
    {
        if (static::needsAsterisk()) {
            $tblRequired = [];
            foreach ($sform->getRequired() as $item) {
                $tblRequired[] = $item->_name;
            }
            $tblElements = [];
            $tblElements =& $sform->getElements();
            $cnt         = count($tblElements);
            for ($i = 0; $i < $cnt; $i++) {
                if (is_object($tblElements[$i]) && in_array($tblElements[$i]->_name, $tblRequired)) {
                    $tblElements[$i]->_caption .= ' *';
                }
            }
        }

        return $sform;
    }

    /**
     * Create an html heading (from h1 to h6)
     *
     * @param string  $title The text to use
     * @param integer $level Level to return
     * @return string The heading
     */
    public static function htitle($title = '', $level = 1)
    {
        printf('<h%01d>%s</h%01d>', $level, $title, $level);
    }

    /**
     * Create a unique upload filename
     *
     * @param string  $folder   The folder where the file will be saved
     * @param string  $fileName Original filename (coming from the user)
     * @param boolean $trimName Do we need to create a short unique name ?
     * @return string The unique filename to use (with its extension)
     */
    public static function createUploadName($folder, $fileName, $trimName = false)
    {
        $workingfolder = $folder;
        if ('/' !== xoops_substr($workingfolder, strlen($workingfolder) - 1, 1)) {
            $workingfolder .= '/';
        }
        $ext  = basename($fileName);
        $ext  = explode('.', $ext);
        $ext  = '.' . $ext[count($ext) - 1];
        $true = true;
        while ($true) {
            $ipbits = explode('.', $_SERVER['REMOTE_ADDR']);
            list($usec, $sec) = explode(' ', microtime());
            $usec = (integer)($usec * 65536);
            $sec  = ((integer)$sec) & 0xFFFF;

            if ($trimName) {
                $uid = sprintf('%06x%04x%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            } else {
                $uid = sprintf('%08x-%04x-%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            }
            if (!file_exists($workingfolder . $uid . $ext)) {
                $true = false;
            }
        }

        return $uid . $ext;
    }

    /**
     * Création d'une titre pour être utilisé par l'url rewriting
     *
     * @param string  $content Le texte à utiliser pour créer l'url
     * @param integer $urw     La limite basse pour créer les mots
     * @return string Le texte à utiliser pour l'url
     */
    public static function makeSeoUrl($content, $urw = 1)
    {
        $s       = "ÀÁÂÃÄÅÒÓÔÕÖØÈÉÊËÇÌÍÎÏÙÚÛÜŸÑàáâãäåòóôõöøèéêëçìíîïùúûüÿñ '()";
        $r       = 'AAAAAAOOOOOOEEEECIIIIUUUUYNaaaaaaooooooeeeeciiiiuuuuyn----';
        $content = strtr($content, $s, $r);
        $content = strip_tags($content);
        $content = strtolower($content);
        $content = htmlentities($content);
        $content = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $content);
        $content = html_entity_decode($content);
        $content = str_replace('quot', ' ', $content);
        $content = str_replace("'", ' ', $content);
        $content = str_replace('-', ' ', $content);
        $content = preg_replace('/[[:punct:]]/i', '', $content);
        // Selon option mais attention au fichier .htaccess !
        // $content = eregi_replace('[[:digit:]]','', $content);
        $content = preg_replace('/[^a-z|A-Z|0-9]/', '-', $content);

        $words    = explode(' ', $content);
        $keywords = '';
        foreach ($words as $word) {
            if (strlen($word) >= $urw) {
                $keywords .= '-' . trim($word);
            }
        }
        if (!$keywords) {
            $keywords = '-';
        }
        // Supprime les tirets en double
        $keywords = str_replace('--', '-', $keywords);
        // Supprime un éventuel tiret à la fin de la chaine
        if ('-' == substr($keywords, strlen($keywords) - 1, 1)) {
            $keywords = substr($keywords, 0, strlen($keywords) - 1);
        }

        return $keywords;
    }

    /**
     * Create the meta keywords based on the content
     *
     * @param string $content Content from which we have to create metakeywords
     * @return string The list of meta keywords
     */
    public static function createMetaKeywords($content)
    {
        $keywordscount = 40;
        $keywordsorder = 0;

        $tmp = [];
        //  Search for the "Minimum keyword length"
        $config_handler    = xoops_getHandler('config');
        $xoopsConfigSearch =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
        $limit             = $xoopsConfigSearch['keyword_min'];

        $myts            = \MyTextSanitizer::getInstance();
        $content         = str_replace('<br>', ' ', $content);
        $content         = $myts->undoHtmlSpecialChars($content);
        $content         = strip_tags($content);
        $content         = strtolower($content);
        $search_pattern  = ['&nbsp;', "\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '-', '_', '\\', '*'];
        $replace_pattern = [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $content         = str_replace($search_pattern, $replace_pattern, $content);
        $keywords        = explode(' ', $content);
        switch ($keywordsorder) {
            case 0:    // Ordre d'apparition dans le texte
                $keywords = array_unique($keywords);
                break;
            case 1:    // Ordre de fréquence des mots
                $keywords = array_count_values($keywords);
                asort($keywords);
                $keywords = array_keys($keywords);
                break;
            case 2:    // Ordre inverse de la fréquence des mots
                $keywords = array_count_values($keywords);
                arsort($keywords);
                $keywords = array_keys($keywords);
                break;
        }
        foreach ($keywords as $keyword) {
            if (strlen($keyword) >= $limit && !is_numeric($keyword)) {
                $tmp[] = $keyword;
            }
        }
        $tmp = array_slice($tmp, 0, $keywordscount);
        if (count($tmp) > 0) {
            return implode(',', $tmp);
        } else {
            if (!isset($config_handler) || !is_object($config_handler)) {
                $config_handler = xoops_getHandler('config');
            }
            $xoopsConfigMetaFooter =& $config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
            if (isset($xoopsConfigMetaFooter['meta_keywords'])) {
                return $xoopsConfigMetaFooter['meta_keywords'];
            } else {
                return '';
            }
        }
    }

    /**
     * Ajoutes les secondes à une heure si elles sont absentes
     *
     * @param string $time L'heure à traiter
     * @return string L'heure sous la forme hh:mm:ss
     */
    public static function normalyzeTime($time)
    {
        if (8 != strlen($time)) {
            return $time . ':00';
        } else {
            return $time;
        }
    }

    public static function textForEmail($chaine)
    {
        $search = $replace = [];
        $chaine = html_entity_decode($chaine);

        for ($i = 0; $i <= 255; $i++) {
            $search[]  = '&#' . $i . ';';
            $replace[] = chr($i);
        }
        $replace[] = '...';
        $search[]  = '…';
        $replace[] = "'";
        $search[]  = '‘';
        $replace[] = "'";
        $search[]  = '’';
        $replace[] = '-';
        $search[]  = '&bull;';    // $replace[] = '•';
        $replace[] = '—';
        $search[]  = '&mdash;';
        $replace[] = '-';
        $search[]  = '&ndash;';
        $replace[] = '-';
        $search[]  = '&shy;';
        $replace[] = '"';
        $search[]  = '&quot;';
        $replace[] = '&';
        $search[]  = '&amp;';
        $replace[] = 'ˆ';
        $search[]  = '&circ;';
        $replace[] = '¡';
        $search[]  = '&iexcl;';
        $replace[] = '¦';
        $search[]  = '&brvbar;';
        $replace[] = '¨';
        $search[]  = '&uml;';
        $replace[] = '¯';
        $search[]  = '&macr;';
        $replace[] = '´';
        $search[]  = '&acute;';
        $replace[] = '¸';
        $search[]  = '&cedil;';
        $replace[] = '¿';
        $search[]  = '&iquest;';
        $replace[] = '˜';
        $search[]  = '&tilde;';
        $replace[] = "'";
        $search[]  = '&lsquo;';    // $replace[]='‘';
        $replace[] = "'";
        $search[]  = '&rsquo;';    // $replace[]='’';
        $replace[] = '‚';
        $search[]  = '&sbquo;';
        $replace[] = "'";
        $search[]  = '&ldquo;';    // $replace[]='“';
        $replace[] = "'";
        $search[]  = '&rdquo;';    // $replace[]='”';
        $replace[] = '„';
        $search[]  = '&bdquo;';
        $replace[] = '‹';
        $search[]  = '&lsaquo;';
        $replace[] = '›';
        $search[]  = '&rsaquo;';
        $replace[] = '<';
        $search[]  = '&lt;';
        $replace[] = '>';
        $search[]  = '>';
        $replace[] = '±';
        $search[]  = '&plusmn;';
        $replace[] = '«';
        $search[]  = '&laquo;';
        $replace[] = '»';
        $search[]  = '&raquo;';
        $replace[] = '×';
        $search[]  = '&times;';
        $replace[] = '÷';
        $search[]  = '&divide;';
        $replace[] = '¢';
        $search[]  = '&cent;';
        $replace[] = '£';
        $search[]  = '&pound;';
        $replace[] = '¤';
        $search[]  = '&curren;';
        $replace[] = '¥';
        $search[]  = '&yen;';
        $replace[] = '§';
        $search[]  = '&sect;';
        $replace[] = '©';
        $search[]  = '&copy;';
        $replace[] = '¬';
        $search[]  = '&not;';
        $replace[] = '®';
        $search[]  = '&reg;';
        $replace[] = '°';
        $search[]  = '&deg;';
        $replace[] = 'µ';
        $search[]  = '&micro;';
        $replace[] = '¶';
        $search[]  = '&para;';
        $replace[] = '·';
        $search[]  = '&middot;';
        $replace[] = '†';
        $search[]  = '&dagger;';
        $replace[] = '‡';
        $search[]  = '&Dagger;';
        $replace[] = '‰';
        $search[]  = '&permil;';
        $replace[] = 'Euro';
        $search[]  = '&euro;';        // $replace[]='€'
        $replace[] = '¼';
        $search[]  = '&frac14;';
        $replace[] = '½';
        $search[]  = '&frac12;';
        $replace[] = '¾';
        $search[]  = '&frac34;';
        $replace[] = '¹';
        $search[]  = '&sup1;';
        $replace[] = '²';
        $search[]  = '&sup2;';
        $replace[] = '³';
        $search[]  = '&sup3;';
        $replace[] = 'á';
        $search[]  = '&aacute;';
        $replace[] = 'Á';
        $search[]  = '&Aacute;';
        $replace[] = 'â';
        $search[]  = '&acirc;';
        $replace[] = 'Â';
        $search[]  = '&Acirc;';
        $replace[] = 'à';
        $search[]  = '&agrave;';
        $replace[] = 'À';
        $search[]  = '&Agrave;';
        $replace[] = 'å';
        $search[]  = '&aring;';
        $replace[] = 'Å';
        $search[]  = '&Aring;';
        $replace[] = 'ã';
        $search[]  = '&atilde;';
        $replace[] = 'Ã';
        $search[]  = '&Atilde;';
        $replace[] = 'ä';
        $search[]  = '&auml;';
        $replace[] = 'Ä';
        $search[]  = '&Auml;';
        $replace[] = 'ª';
        $search[]  = '&ordf;';
        $replace[] = 'æ';
        $search[]  = '&aelig;';
        $replace[] = 'Æ';
        $search[]  = '&AElig;';
        $replace[] = 'ç';
        $search[]  = '&ccedil;';
        $replace[] = 'Ç';
        $search[]  = '&Ccedil;';
        $replace[] = 'ð';
        $search[]  = '&eth;';
        $replace[] = 'Ð';
        $search[]  = '&ETH;';
        $replace[] = 'é';
        $search[]  = '&eacute;';
        $replace[] = 'É';
        $search[]  = '&Eacute;';
        $replace[] = 'ê';
        $search[]  = '&ecirc;';
        $replace[] = 'Ê';
        $search[]  = '&Ecirc;';
        $replace[] = 'è';
        $search[]  = '&egrave;';
        $replace[] = 'È';
        $search[]  = '&Egrave;';
        $replace[] = 'ë';
        $search[]  = '&euml;';
        $replace[] = 'Ë';
        $search[]  = '&Euml;';
        $replace[] = 'ƒ';
        $search[]  = '&fnof;';
        $replace[] = 'í';
        $search[]  = '&iacute;';
        $replace[] = 'Í';
        $search[]  = '&Iacute;';
        $replace[] = 'î';
        $search[]  = '&icirc;';
        $replace[] = 'Î';
        $search[]  = '&Icirc;';
        $replace[] = 'ì';
        $search[]  = '&igrave;';
        $replace[] = 'Ì';
        $search[]  = '&Igrave;';
        $replace[] = 'ï';
        $search[]  = '&iuml;';
        $replace[] = 'Ï';
        $search[]  = '&Iuml;';
        $replace[] = 'ñ';
        $search[]  = '&ntilde;';
        $replace[] = 'Ñ';
        $search[]  = '&Ntilde;';
        $replace[] = 'ó';
        $search[]  = '&oacute;';
        $replace[] = 'Ó';
        $search[]  = '&Oacute;';
        $replace[] = 'ô';
        $search[]  = '&ocirc;';
        $replace[] = 'Ô';
        $search[]  = '&Ocirc;';
        $replace[] = 'ò';
        $search[]  = '&ograve;';
        $replace[] = 'Ò';
        $search[]  = '&Ograve;';
        $replace[] = 'º';
        $search[]  = '&ordm;';
        $replace[] = 'ø';
        $search[]  = '&oslash;';
        $replace[] = 'Ø';
        $search[]  = '&Oslash;';
        $replace[] = 'õ';
        $search[]  = '&otilde;';
        $replace[] = 'Õ';
        $search[]  = '&Otilde;';
        $replace[] = 'ö';
        $search[]  = '&ouml;';
        $replace[] = 'Ö';
        $search[]  = '&Ouml;';
        $replace[] = 'œ';
        $search[]  = '&oelig;';
        $replace[] = 'Œ';
        $search[]  = '&OElig;';
        $replace[] = 'š';
        $search[]  = '&scaron;';
        $replace[] = 'Š';
        $search[]  = '&Scaron;';
        $replace[] = 'ß';
        $search[]  = '&szlig;';
        $replace[] = 'þ';
        $search[]  = '&thorn;';
        $replace[] = 'Þ';
        $search[]  = '&THORN;';
        $replace[] = 'ú';
        $search[]  = '&uacute;';
        $replace[] = 'Ú';
        $search[]  = '&Uacute;';
        $replace[] = 'û';
        $search[]  = '&ucirc;';
        $replace[] = 'Û';
        $search[]  = '&Ucirc;';
        $replace[] = 'ù';
        $search[]  = '&ugrave;';
        $replace[] = 'Ù';
        $search[]  = '&Ugrave;';
        $replace[] = 'ü';
        $search[]  = '&uuml;';
        $replace[] = 'Ü';
        $search[]  = '&Uuml;';
        $replace[] = 'ý';
        $search[]  = '&yacute;';
        $replace[] = 'Ý';
        $search[]  = '&Yacute;';
        $replace[] = 'ÿ';
        $search[]  = '&yuml;';
        $replace[] = 'Ÿ';
        $search[]  = '&Yuml;';
        $replace[] = "\n";
        $search[]  = '<br>';
        $chaine    = str_replace($search, $replace, $chaine);

        return $chaine;
    }
}
