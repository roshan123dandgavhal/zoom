# zoom
Zoom Implementation
Implement Zoom API with PHP

First Login zoom.us using your login credentials.

Then Goto
https://marketplace.zoom.us

Create App

Choose your app type = OAuth

After Create App You will get CLIENT ID and CLIENT SECRET KEY. Save thse detail to notepad for api use.
Sample details for create APP

USE CLIENT ID and CLIENT SECRET KEY in config.php file

Create following table in datbase

DROP TABLE IF EXISTS `tbl_zoom_setup`;
CREATE TABLE IF NOT EXISTS `tbl_zoom_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` text NOT NULL,
  `refresh_token` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

Update databse details into class-db.php

Run index.php file
