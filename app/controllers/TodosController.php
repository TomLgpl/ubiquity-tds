<?php

/**
 * Controlle TodosController
 * @property \Ajax\php\ubiquity\JsUtils $jquery
 */

namespace controllers;
use http\QueryString;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\controllers\Router;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;

class TodosController extends ControllerBase{

    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID = 'not saved';
    const LIST_SESSION_KEY = 'list';
    const ACTIVE_LIST_SESSION_KEY = 'active-lsit';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->menu();
    }

    private function menu(){
        $this->loadView('TodosController/menu.html');
    }

    #[Route('_default', name: "home")]
	public function index(){
        if(USession::exists(self::LIST_SESSION_KEY)){
            $list = USession::get(self::LIST_SESSION_KEY, []);
            return $this->display($list);
        }
        $this->showMessage('Bienvenue !', 'TodoLists permet de générer des listes ...', 'info', 'info circle outline',
            [['url' => Router::path('todos.new'), 'caption' => 'Créer une nouvelle liste', 'style' => 'basic inverted']]);
	}

	private function display(array $list){
        $this->loadView('TodosController/display.html', ['list' => $list]);
    }

	#[Post(path: "todos/add", name: "todos.name")]
    public function addElement(){
        $list = USession::get(self::LIST_SESSION_KEY);
        if(URequest::filled('elements')){
            $elements = explode("\n", URequest::post('elements'));
            foreach ($elements as $element) {
                $list[] = $element;
            }
        }
        if(URequest::filled('element')){
            $list[] = URequest::post('element');
        }
        USession::set(self::LIST_SESSION_KEY, $list);
        $this->display($list);
    }

    #[Get(path: "todos/delete/{index}", name: "todos.delete")]
	public function deleteElement($index){
		
	}

	#[Post(path: "todos/edit/{index}", name: "todos.edit")]
    public function editElement($index){

    }

    #[Get(path: "todos/loadList/{uniqid}", name: "todos.loadList")]
    public function loadList($uniqid){

    }

    #[Post(path: "todos/loadList", name: "todos.loadListPost")]
    public function loadListFromFrom(){

    }

    #[Get(path: "todos/new/{force}", name: "todos.new")]
    public function newList($force = false){
        if(USession::exists(self::LIST_SESSION_KEY) && $force == false){
            $this->showMessage("Nouvelle Liste", "Une liste existe déjà. Voulez-vous la vider ?", "", "",
                [['url' => Router::path('todos.new/1'), 'caption' => 'Créer une nouvelle liste', 'style' => 'basic inverted'],
                ['url' => Router::path('todos.menu'), 'caption' => 'Annuler', 'style' => 'basic inverted']]);
        }
        else {
            $this->showMessage("Nouvelle Liste", "Une nouvelle liste a été crée !", "", "check square outline");
            $list = [];
            USession::set(self::ACTIVE_LIST_SESSION_KEY, $list);
            $this->display($list);
        }
    }

    #[Get(path: "todos/saveList", name: "todos.save")]
    public function saveList(){

    }

    private function showMessage(string $header, string $message, string $type = 'info', string $icon = 'info circle', array $buttons = []){
        $this->loadView('main/showMessage.html', compact('header', 'type', 'icon', 'message', 'buttons'));
    }

}
