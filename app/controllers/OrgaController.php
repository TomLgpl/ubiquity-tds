<?php

namespace controllers;

 use models\Organization;
 use Ubiquity\orm\DAO;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\repositories\ViewRepository;

 /**
  * Controller OrgaController
  */
class OrgaController extends ControllerBase{

    private ViewRepository $repo;

    public function initialize(){
        parent::initialize();
        $this->repo??=new ViewRepository($this, Organization::class);
    }

    #[Route('orga')]
	public function index(){
        //$orgas = DAO::getAll(Organization::class, "", false);
        $this->repo->all("", false, [], false, 'orgas');
		$this->loadView("OrgaController/index.html");
	}

	#[Route(path: "orga/{idOrga}",name: "orga.getOne")]
	public function getOne($idOrga){
        //$orga = DAO::getById(Organization::class, $idOrga, ['groupes.users', 'users.groupes']);
        $this->repo->byId($idOrga, ['users.groupes', 'groupes.users'], false, 'orga');
		$this->loadDefaultView();
	}

}
