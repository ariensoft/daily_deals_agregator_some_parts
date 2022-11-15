<?php

mysql_connect("localhost", "root", "passwd here") or die("Nelze se připojit k MySQL: " . mysql_error());
mysql_select_db("ms_import") or die("Nelze vybrat databázi: " . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");

?>
