<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 13.11.17
 * Time: 14:46
 */

require 'config/config.php';
require "Controller/IndexController.php";
require "Controller/ModelFactory.php";
require "Controller/PageModel.php";
require "Controller/View.php";
require "Controller/DB.php";
require "Controller/Paginator.php";
require 'Pagination/Pagination.class.php';

$indexController = new Controller\IndexController();
$getInstance = $indexController->init();