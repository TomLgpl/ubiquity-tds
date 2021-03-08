<?php
namespace controllers;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;

 /**
  * Controller MainController
  */
class MainController extends ControllerBase{

    use WithAuthTrait;

	public function index(){
		$this->loadView("MainController/index.html");
	}

    protected function getAuthController(): AuthController {
        return new Authentification($this);
    }

}
