<?php

use \Sincco\Sfphp\Config\Reader;
use \Sincco\Sfphp\Request;
use \Sincco\Sfphp\Response;
use \Sincco\Tools\Tokenizer;

class V1Controller extends Sincco\Sfphp\Abstracts\Controller 
{
    private function validateToken() {
        $password = Reader::get('app')['key'];
        $valid = Tokenizer::validate(Request::get('authorization'), $password);
        if (!$valid) {
            new Response('json', ['respuesta'=>false, 'extra'=>'Token no válido']);
            die();
        } else {
            return $valid;
        }
    }

    public function token() {
        switch (Request::get('method')) {
            case 'POST':
                $userData = ['email'=>$this->getParams('email'),'password'=>$this->getParams('password')];
                $model = $this->getModel('Api\UsersApi');
                $respuesta = $model->validateAccess($userData);
                if(count($respuesta)) {
                    $password = Reader::get('app')['key'];
                    $minutesExpiration = 2880;
                    unset($userData['password']);
                    $token = Tokenizer::create( $userData, $password, $minutesExpiration );
                    new Response('json', ['token'=>$token, 'extra'=>'']);
                } else {
                    new Response('json', ['token'=>false, 'extra'=>'Usuario no válido']);
                }
                break;
            default:
                new Response('json', ['token'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
                break;
        }
    }

    public function contratos() {
        $pagination = [0];
        $token = $this->validateToken();
        $contratos = $this->getModel('Expedientes\Contratos');
        switch (Request::get('method')) {
            case 'GET':
                if (isset($_GET['pagination'])) {
                    $pagination = $_GET['pagination'];
                }
                $filters = [];
                if (isset($_GET['filters'])) {
                    $filters = $_GET['filters'];
                }
                $data = $contratos->getDataFiltered($filters, $pagination);
                $total = $contratos->getTotalDataFiltered($filters);
		#var_dump($data);
		$data = $this->cleanArray($data);
		#var_dump($data);
                new Response('json', ['data'=>$data, 'registros'=>$total, 'extra'=>'']);
                break;
            default:
                new Response('json', ['data'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
                break;
        }
    }

    public function imagenes() {
        $token = $this->validateToken();
        $contratos = $this->getModel('Expedientes\Contratos');
        switch (Request::get('method')) {
            case 'GET':
                $contrato = $this->getParams('contrato');
                if (is_dir(PATH_ROOT . '/_expedientes/')) {
                    $files = scandir(PATH_ROOT . '/_expedientes/' . $contrato);
                    array_shift($files);
                    array_shift($files);
                    $adjuntos = array();
                    foreach ($files as $adjunto) {
                        array_push($adjuntos, str_replace(' ', '%20', $adjunto));
                    }
                    new Response('json', [ 'data'=>$adjuntos, 'extra'=>BASE_URL . '_expedientes/' . $contrato .'/' ]);
                } else {
                    new Response('json', [ 'data'=>false, 'extra'=>'El contrato no tiene imagenes asignadas.' ]);
                }
                break;
            default:
                new Response('json', ['data'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
                break;
        }
    }

    private function cleanArray($array) {
        if (is_array($array))
        {
            foreach ($array as $key => $sub_array)
            {
                $result = $this->cleanArray($sub_array);
                $array[$key] = $result;
            }
        }

        if (empty($array))
        {
            return 0;
        }

        return $array;
    }

}
