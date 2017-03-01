<?php

use \Sincco\Sfphp\Response;

class LecturaController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$userData = $this->helper('UsersAccount')->getUserData('user\extra');
		$view = $this->newView('Cobranza\Lectura');
		$view->cobros = $this->getModel('Catalogos\Cobros')->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

	public function grid() {
		$this->helper('UsersAccount')->checkLogin();
		$userData = $this->helper('UsersAccount')->getUserData('user\extra');
		$view = $this->newView('Cobranza\Grid');
		$view->lecturas = $this->getModel('Aguas')->contratosLecturas()->join('contratosImages img','maintable.contrato=img.contrato AND maintable.fechaLectura=img.fecha')->where('imagen','lectura-medidor', '=', 'img')->getData();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}

}
