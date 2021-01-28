<?php

namespace controllers;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\attributes\items\router\Get;

class TodosController extends ControllerBase{

    #[Route('_default', name: "home")]
	public function index(){
		
	}

	#[Post(path: "todos/add", name: "todos.name")]
    public function addElement(){

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
    public function newList($force){

    }

    #[Get(path: "todos/saveList", name: "todos.save")]
    public function saveList(){

    }

}
