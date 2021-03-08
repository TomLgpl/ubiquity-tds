<?php
namespace controllers\auth\files;

use Ubiquity\controllers\auth\AuthFiles;
 /**
  * Class AuthentificationFiles
  */
class AuthentificationFiles extends AuthFiles{
	public function getViewIndex(){
		return "Authentification/index.html";
	}

	public function getViewInfo(){
		return "Authentification/info.html";
	}

	public function getViewNoAccess(){
		return "Authentification/noAccess.html";
	}

	public function getViewDisconnected(){
		return "Authentification/disconnected.html";
	}

	public function getViewMessage(){
		return "Authentification/message.html";
	}


}
