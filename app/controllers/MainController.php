<?php
namespace controllers;
use models\Basket;
use models\Orderdetail;
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
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.quantity', 'basketdetails.product.price'], [USession::get("idUser")]);
        $promotions = DAO::getAll(Product::class, 'promotion <> 0.00', ['section']);
        $nbArticles = 0;
        $prixPanier = 0;
        if(!empty($basket)) {
            foreach($basket->getBasketdetails() as $detail)
              $nbArticles += $detail->getQuantity();
            foreach($basket->getBasketDetails() as $detail)
             $prixPanier = $detail->getQuantity() * $detail->getProduct()->getPrice();
        }
        $this->loadView("MainController/index.html", ['user' => $user, 'promotions' => $promotions, 'nbArticles' => $nbArticles, 'prixPanier' => $prixPanier]);
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

    #[Get(path: "store/orders/{idUser}", name: "store.orders")]
    public function orders($idUser) {
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [$idUser]);
        $this->jquery->renderView("MainController/orders.html", ['orders' => $orders]);
    }

    #[Get(path: "store/order/{idOrder}", name: "store.order")]
    public function order($idOrder){
        $user = DAO::getById(User::class, [USession::get('idUser')]);
        $order = DAO::getById(Order::class, $idOrder);
        $orderdetails = DAO::getAll(Orderdetail::class, 'idOrder= ?', ['product'], [$idOrder]);
        $this->jquery->renderView("MainController/order.html", ['user' => $user, 'order' => $order, 'orderDetails' => $orderdetails]);
    }

}
