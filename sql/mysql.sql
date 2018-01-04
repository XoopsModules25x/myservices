CREATE TABLE `myservices_caddy` (
  `caddy_id` int(10) unsigned NOT NULL auto_increment,
  `caddy_products_id` int(10) unsigned NOT NULL,
  `caddy_employes_id` int(10) unsigned NOT NULL,
  `caddy_calendar_id` int(10) unsigned NOT NULL,
  `caddy_orders_id` int(10) unsigned NOT NULL,
  `caddy_price` decimal(7,2) NOT NULL,
  `caddy_vat_rate` double(5,2) NOT NULL,
  `caddy_start` datetime NOT NULL,
  `caddy_end` datetime NOT NULL,
  PRIMARY KEY  (`caddy_id`),
  KEY `caddy_products_id` (`caddy_products_id`),
  KEY `caddy_employes_id` (`caddy_employes_id`),
  KEY `caddy_calendar_id` (`caddy_calendar_id`),
  KEY `caddy_orders_id` (`caddy_orders_id`)
) ENGINE=InnoDB;


CREATE TABLE `myservices_calendar` (
  `calendar_id` int(10) unsigned NOT NULL auto_increment,
  `calendar_status` tinyint(1) unsigned NOT NULL COMMENT '1=Travail, 2=Absence',
  `calendar_employes_id` int(10) unsigned NOT NULL,
  `calendar_start` datetime NOT NULL,
  `calendar_end` datetime NOT NULL,
  `calendar_products_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`calendar_id`),
  KEY `calendar_status` (`calendar_status`),
  KEY `calendar_employes_id` (`calendar_employes_id`),
  KEY `calendar_start` (`calendar_start`),
  KEY `calendar_end` (`calendar_end`)
) ENGINE=InnoDB COMMENT='Indique les commandes comme les absences';


CREATE TABLE `myservices_categories` (
  `categories_id` int(10) unsigned NOT NULL auto_increment,
  `categories_pid` int(10) unsigned NOT NULL,
  `categories_title` varchar(255) NOT NULL,
  `categories_imgurl` varchar(255) NOT NULL,
  `categories_description` text NOT NULL,
  `categories_advertisement` text NOT NULL,
  PRIMARY KEY  (`categories_id`),
  KEY `categories_pid` (`categories_pid`),
  KEY `categories_title` (`categories_title`)
) ENGINE=InnoDB;


CREATE TABLE `myservices_employes` (
  `employes_id` int(10) unsigned NOT NULL auto_increment,
  `employes_firstname` varchar(50) NOT NULL,
  `employes_lastname` varchar(50) NOT NULL,
  `employes_email` varchar(150) NOT NULL,
  `employes_bio` text NOT NULL,
  `employes_photo1` varchar(255) NOT NULL,
  `employes_photo2` varchar(255) NOT NULL,
  `employes_photo3` varchar(255) NOT NULL,
  `employes_photo4` varchar(255) NOT NULL,
  `employes_photo5` varchar(255) NOT NULL,
  `employes_isactive` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`employes_id`),
  KEY `employes_firstname` (`employes_firstname`),
  KEY `employes_lastname` (`employes_lastname`),
  KEY `employes_isactive` (`employes_isactive`)
) ENGINE=InnoDB;


CREATE TABLE `myservices_employesproducts` (
  `employesproducts_id` int(10) unsigned NOT NULL auto_increment,
  `employesproducts_employes_id` int(10) unsigned NOT NULL,
  `employesproducts_products_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`employesproducts_id`),
  KEY `employesproducts_employes_id` (`employesproducts_employes_id`),
  KEY `employesproducts_products_id` (`employesproducts_products_id`)
) ENGINE=InnoDB;


CREATE TABLE `myservices_orders` (
  `orders_id` int(10) unsigned NOT NULL auto_increment,
  `orders_uid` int(10) unsigned NOT NULL,
  `orders_date` datetime NOT NULL,
  `orders_state` tinyint(1) unsigned NOT NULL,
  `orders_ip` varchar(40) NOT NULL,
  `orders_firstname` varchar(50) NOT NULL,
  `orders_lastname` varchar(50) NOT NULL,
  `orders_address` text NOT NULL,
  `orders_zip` varchar(30) NOT NULL,
  `orders_town` varchar(255) NOT NULL,
  `orders_country` varchar(3) NOT NULL,
  `orders_telephone` varchar(30) NOT NULL,
  `orders_email` varchar(255) NOT NULL,
  `orders_articles_count` mediumint(8) unsigned NOT NULL,
  `orders_total` double(7,2) NOT NULL,
  `orders_password` varchar(32) NOT NULL COMMENT 'Used to view the bill online',
  `orders_cancel` varchar(32) NOT NULL COMMENT 'Used to cancel the entire order',
  PRIMARY KEY  (`orders_id`),
  KEY `orders_date` (`orders_date`),
  KEY `orders_state` (`orders_state`),
  KEY `orders_password` (`orders_password`),
  KEY `orders_cancel` (`orders_cancel`)
) ENGINE=InnoDB;

CREATE TABLE `myservices_prefs` (
  `prefs_id` mediumint(8) unsigned NOT NULL auto_increment,
  `prefs_j1t1debut` time NOT NULL default '00:00:00',
  `prefs_j1t1fin` time NOT NULL default '00:00:00',
  `prefs_j1t2debut` time NOT NULL default '00:00:00',
  `prefs_j1t2fin` time NOT NULL default '00:00:00',
  `prefs_j2t1debut` time NOT NULL default '00:00:00',
  `prefs_j2t1fin` time NOT NULL default '00:00:00',
  `prefs_j2t2debut` time NOT NULL default '00:00:00',
  `prefs_j2t2fin` time NOT NULL default '00:00:00',
  `prefs_j3t1debut` time NOT NULL default '00:00:00',
  `prefs_j3t1fin` time NOT NULL default '00:00:00',
  `prefs_j3t2debut` time NOT NULL default '00:00:00',
  `prefs_j3t2fin` time NOT NULL default '00:00:00',
  `prefs_j4t1debut` time NOT NULL default '00:00:00',
  `prefs_j4t1fin` time NOT NULL default '00:00:00',
  `prefs_j4t2debut` time NOT NULL default '00:00:00',
  `prefs_j4t2fin` time NOT NULL default '00:00:00',
  `prefs_j5t1debut` time NOT NULL default '00:00:00',
  `prefs_j5t1fin` time NOT NULL default '00:00:00',
  `prefs_j5t2debut` time NOT NULL default '00:00:00',
  `prefs_j5t2fin` time NOT NULL default '00:00:00',
  `prefs_j6t1debut` time NOT NULL default '00:00:00',
  `prefs_j6t1fin` time NOT NULL default '00:00:00',
  `prefs_j6t2debut` time NOT NULL default '00:00:00',
  `prefs_j6t2fin` time NOT NULL default '00:00:00',
  `prefs_j7t1debut` time NOT NULL default '00:00:00',
  `prefs_j7t1fin` time NOT NULL default '00:00:00',
  `prefs_j7t2debut` time NOT NULL default '00:00:00',
  `prefs_j7t2fin` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`prefs_id`)
) ENGINE=InnoDB;

CREATE TABLE `myservices_products` (
  `products_id` int(10) unsigned NOT NULL auto_increment,
  `products_vat_id` mediumint(8) unsigned NOT NULL,
  `products_categories_id` int(10) unsigned NOT NULL,
  `products_title` varchar(255) NOT NULL,
  `products_online` tinyint(1) unsigned NOT NULL,
  `products_price` decimal(7,2) NOT NULL,
  `products_summary` text NOT NULL,
  `products_description` text NOT NULL,
  `products_quality_link` varchar(255) NOT NULL,
  `products_image1` varchar(255) NOT NULL,
  `products_image2` varchar(255) NOT NULL,
  `products_image3` varchar(255) NOT NULL,
  `products_image4` varchar(255) NOT NULL,
  `products_image5` varchar(255) NOT NULL,
  `products_image6` varchar(255) NOT NULL,
  `products_image7` varchar(255) NOT NULL,
  `products_image8` varchar(255) NOT NULL,
  `products_image9` varchar(255) NOT NULL,
  `products_image10` varchar(255) NOT NULL,
  `products_duration` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`products_id`),
  KEY `products_vat_id` (`products_vat_id`),
  KEY `products_categories_id` (`products_categories_id`),
  KEY `products_title` (`products_title`),
  KEY `products_online` (`products_online`),
  KEY `products_duration` (`products_duration`)
) ENGINE=InnoDB;

CREATE TABLE `myservices_vat` (
  `vat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `vat_rate` double(5,2) NOT NULL,
  PRIMARY KEY  (`vat_id`),
  KEY `vat_rate` (`vat_rate`)
) ENGINE=InnoDB;


INSERT INTO `myservices_prefs` (`prefs_id`, `prefs_j1t1debut`, `prefs_j1t1fin`, `prefs_j1t2debut`, `prefs_j1t2fin`, `prefs_j2t1debut`, `prefs_j2t1fin`, `prefs_j2t2debut`, `prefs_j2t2fin`, `prefs_j3t1debut`, `prefs_j3t1fin`, `prefs_j3t2debut`, `prefs_j3t2fin`, `prefs_j4t1debut`, `prefs_j4t1fin`, `prefs_j4t2debut`, `prefs_j4t2fin`, `prefs_j5t1debut`, `prefs_j5t1fin`, `prefs_j5t2debut`, `prefs_j5t2fin`, `prefs_j6t1debut`, `prefs_j6t1fin`, `prefs_j6t2debut`, `prefs_j6t2fin`, `prefs_j7t1debut`, `prefs_j7t1fin`, `prefs_j7t2debut`, `prefs_j7t2fin`) VALUES(1, '08:00:00', '12:00:00', '14:00:00', '18:00:00', '08:00:00', '12:00:00', '14:00:00', '18:00:00', '08:00:00', '12:00:00', '14:00:00', '18:00:00', '08:00:00', '12:00:00', '14:00:00', '18:00:00', '08:00:00', '12:00:00', '14:00:00', '18:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00');
