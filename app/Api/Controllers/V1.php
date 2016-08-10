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
        $pagination = [1];
        $token = $this->validateToken();
        $contratos = $this->getModel('Expedientes\Contratos');
        switch (Request::get('method')) {
            case 'GET':
                if (isset($_GET['pagination'])) {
                    $pagination = $_GET['pagination'];
                }
                $data = $contratos->getDataFiltered($_GET['filters'], $pagination);
                $total = $contratos->getTotalDataFiltered($_GET['filters']);
                new Response('json', ['data'=>$data, 'registros'=>$total, 'extra'=>'']);
                break;
            default:
                new Response('json', ['data'=>false, 'extra'=>'Método ' . Request::get('method') . ' no soportado']);
                break;
        }
    }

}