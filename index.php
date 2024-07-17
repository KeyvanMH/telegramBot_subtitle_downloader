<?php
//all request's come to this page
require_once "config.php";
require_once "model/model.php";
require_once "controller/controller.php";
require_once "view/view.php";
//get any input from the bot
$request =  file_get_contents("php://input");
$controller = new controller($request);
$model = $controller->model ? new model($controller):false;
$view = $model ? new view($model) : new view($controller);
