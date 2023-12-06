<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerSignIn extends Controller
{

    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("notes", "notes");
        } else {
            (new View("index"))->show();
        }
    }
}