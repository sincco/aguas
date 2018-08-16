
<?php
use \Sincco\Sfphp\Response;
class EmpresasController extends Sincco\Sfphp\Abstracts\Controller {
	public function index() {
		$this->helper('UsersAccount')->checkLogin();
		$model = $this->getModel('Catalogos\Empresas');
		$view = $this->newView('Catalogos\EmpresasTabla');
		$view->empresas = $model->getAll();
		$view->menus = $this->helper('UsersAccount')->createMenus();
		$view->render();
	}
	public function api() {
		$empresa = $this->getParams('empresa');
		$model = $this->getModel('Catalogos\Empresas');
		$cuadrillaId = $model->insert($empresa);
		new Response('json', ['respuesta'=>$cuadrillaId]);
	}
}
