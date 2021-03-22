<?php
namespace controllers;
use models\Order;
use models\Product;
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
        $user = DAO::getById(User::class, [USession::get('idUser')], ['orders', 'baskets', ]);
        $promotions = DAO::getAll(Product::class, 'promotion <> 0.00', ['section']);
        $this->loadView("MainController/index.html", ['user' => $user, 'promotions' => $promotions]);
    }

    protected function getAuthController(): AuthController {
        return new Authentification($this);
    }

}
