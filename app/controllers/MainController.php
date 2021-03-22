<?php
namespace controllers;
 use models\Order;
 use models\User;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\http\USession;

 /**
  * Controller MainController
  */
class MainController extends ControllerBase{

    use WithAuthTrait;

	public function index(){
        $user = DAO::getById(User::class, [USession::get('idUser')], ['orders']);
		$this->loadView("MainController/index.html", ['user' => $user]);
	}

    protected function getAuthController(): AuthController {
        return new Authentification($this);
    }

}
