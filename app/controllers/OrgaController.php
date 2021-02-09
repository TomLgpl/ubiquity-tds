<?php

namespace controllers;

 use models\Organization;
 use Ubiquity\orm\DAO;
 use Ubiquity\attributes\items\router\Route;

 /**
  * Controller OrgaController
  */
class OrgaController extends ControllerBase{

    #[Route('orga')]
	public function index(){
	    $orgas = DAO::getAll(Organization::class, "", false);
		$this->loadView("OrgaController/index.html", ['orgas'=>$orgas]);
	}

	#[Route(path: "orga/{idOrga}",name: "orga.getOne")]
	public function getOne($idOrga){
		$orga = DAO::getById(Organization::class, $idOrga, ['groupes.users', 'users.groupes']);
		$this->loadDefaultView(['orga'=>$orga]);
	}

}
