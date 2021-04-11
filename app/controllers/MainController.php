<?php
namespace controllers;
use models\Section;
use Ubiquity\attributes\items\router\Get;
use models\Order;
use models\Product;
use models\User;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\USession;

/**
 * Controller MainController
 * @property JsUtils $jquery
 */
class MainController extends ControllerBase{

    use WithAuthTrait;

    #[Route(path:"_default", name:"home")]
    public function index(){
        $user = DAO::getById(User::class, [USession::get('idUser')], ['orders', 'baskets', ]);
        $promotions = DAO::getAll(Product::class, 'promotion <> 0.00', ['section']);
        $this->loadView("MainController/index.html", ['user' => $user, 'promotions' => $promotions]);
    }

    protected function getAuthController(): AuthController {
        return new Authentification($this);
    }

	#[Get(path: "store",name: "store")]
	public function store(){
        $sections = DAO::getAll(Section::class);
        $promotions = DAO::getAll(Product::class, 'promotion <> 0.00', ['section']);
        $this->jquery->getHref('a[data-target]', parameters: [ 'historize' => false, 'hasLoader' => 'internal','listenerOn' => 'body']);
        $this->jquery->renderView("MainController/store.html", ['sections' => $sections, 'promotions' => $promotions]);
	}


	#[Get(path: "store/section/{id}", name : "store.section")]
	public function section($id){
        $section = DAO::getById(Section::class, $id, ['products']);
        $this->jquery->renderView("MainController/section.html", ['section' => $section]);
	}

	#[Get(path: "store/product/{idSection}/{idProduct}", name: "store.product")]
    public function product($idSection, $idProduct){
        $section = DAO::getById(Section::class, $idSection);
        $product = DAO::getById(Product::class, $idProduct);
        $this->jquery->renderView("MainController/product.html", ['product' => $product, 'section' => $section]);
    }

}
