<?php
namespace controllers;
use models\Basket;
use models\Basketdetail;
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
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.product'], [USession::get("idUser")]);
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

	#[Get(path: "store", name: "store")]
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

    #[Get(path: "store/orders", name: "store.orders")]
    public function orders() {
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->jquery->renderView("MainController/orders.html", ['orders' => $orders]);
    }

    #[Get(path: "store/order/{idOrder}", name: "store.order")]
    public function order($idOrder) {
        $user = DAO::getById(User::class, [USession::get('idUser')]);
        $order = DAO::getById(Order::class, $idOrder);
        $orderdetails = DAO::getAll(Orderdetail::class, 'idOrder= ?', ['product'], [$idOrder]);
        $this->jquery->renderView("MainController/order.html", ['user' => $user, 'order' => $order, 'orderDetails' => $orderdetails]);
    }

    #[Get(path: "store/basket", name: "store.basket")]
    public function basket() {
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.product'], [USession::get("idUser")]);
        $prixPanier = 0;
        if(!empty($basket)) {
            foreach($basket->getBasketDetails() as $detail)
                $prixPanier = $detail->getQuantity() * $detail->getProduct()->getPrice();
        }
        $this->jquery->renderView("MainController/basket.html", ['basket' => $basket, 'prixPanier' => $prixPanier]);
    }

    #[Get(path: "store/basket/more/{idProduct}", name: "store.basket.more")]
    public function more($idProduct) {
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.product'], [USession::get("idUser")]);
        if($this->basketHasProduct($basket, $idProduct)){
            $basketDetail = DAO::getOne(Basketdetail::class, 'idproduct= ? and idbasket= ?', false, [$idProduct, $basket->getId()]);
            $basketDetail->setQuantity($basketDetail->getQuantity() + 1);
            DAO::save($basketDetail);
        }
        $this->basket();
    }

    #[Get(path: "store/basket/less/{idProduct}", name: "store.basket.less")]
    public function less($idProduct) {
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.product'], [USession::get("idUser")]);
        if($this->basketHasProduct($basket, $idProduct)) {
            $basketDetail = DAO::getOne(Basketdetail::class, 'idproduct= ? and idbasket= ?', false, [$idProduct, $basket->getId()]);
            if($basketDetail->getQuantity() == 1) {
                DAO::remove($basketDetail);
            }
            else {
                $basketDetail->setQuantity($basketDetail->getQuantity() - 1);
                DAO::save($basketDetail);
            }
        }
        $this->basket();
    }

    #[Get(path: "store/product/add/{idProduct}", name: "store.product.add")]
    public function add($idProduct) {
        $basket = DAO::getOne(Basket::class, 'idUser= ?', ['basketdetails', 'basketdetails.product'], [USession::get("idUser")]);
        $basketDetail = DAO::getOne(Basketdetail::class, 'idproduct= ? and idbasket= ?', false, [$idProduct, $basket->getId()]);
        if(!$this->basketHasProduct()) {
            $basketDetail->setIdBasket($basket->getId());
            $basketDetail->setIdProduct($idProduct);
            $basketDetail->setQuantity(1);
            DAO::save($basketDetail);
        }
        else {
            $basketDetail = DAO::getOne(Basketdetail::class, 'idproduct= ? and idbasket= ?', false, [$idProduct, $basket->getId()]);
            $basketDetail->setQuantity($basketDetail->getQuantity() + 1);
            DAO::save($basketDetail);
        }
        $this->store();
    }

    private function basketHasProduct($basket, $idProduct) {
        foreach($basket->getBasketDetails() as $detail){
            if($detail->getProduct()->getId() == $idProduct)
                return true;
        }
        return false;
    }

}
