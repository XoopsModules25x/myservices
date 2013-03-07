<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

class myservices_registryfile {
	var $filename;	// Nom du fichier à traiter

	/**
	 * Access the only instance of this class
     *
     * @return	object
     *
     * @static
     * @staticvar   object
	 */
	function &getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new myservices_registryfile();
		}
		return $instance;
	}


	function __construct($fichier = null)
	{
		$this->setfile($fichier);
  	}

	function setfile($fichier = null)
	{
		if($fichier) {
	  		$this->filename = XOOPS_UPLOAD_PATH.'/'.$fichier;
	  	}
	}

	function getfile($fichier = null)
  	{
		$fw = '';
		if(!$fichier) {
			$fw = $this->filename;
		} else {
			$fw = XOOPS_UPLOAD_PATH.'/'.$fichier;
		}
		if(file_exists($fw)) {
			return file_get_contents($fw);
		} else {
			return '';
		}
  	}

  	function savefile($content, $fichier = null)
  	{
		$fw = '';
		if(!$fichier) {
			$fw = $this->filename;
		} else {
			$fw = XOOPS_UPLOAD_PATH.'/'.$fichier;
		}
		if(file_exists($fw)) {
			@unlink($fw);
		}
		$fp = fopen($fw, 'w') or die("Error, impossible to create the file ".$this->filename);
		fwrite($fp, $content);
		fclose($fp);
		return true;
  	}
}
?>