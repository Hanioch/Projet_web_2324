<?php
require_once "framework/Controller.php";
require_once 'framework/View.php';
require_once 'framework/Model.php';

class ControllerSignup extends Controller {
    public function index() : void {
        (new View("signup"))->show();
    }

}