<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


2022-05-30 17:03:18 --> mysqli_sql_exception
文件: D:\phpstudy_pro\WWW\tmcms92.com\mysoul\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tmcms92_com.mys_en_member' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://en.tmcms92.com/admin.php?c=home&m=home
来源: http://en.tmcms92.com/admin.php?c=login


