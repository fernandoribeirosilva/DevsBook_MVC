<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class LoginController extends Controller {

  public function signin() {
    // verificar se tem mensagem de flash(error)
    $flash = '';
    if(!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';// apaga a mensagem 
    }

    $this->render('login', [
      'flash' => $flash
    ]);
  }

  // verificando a senha e email do usuario 
  public function signinAction() {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');

    if($email && $password) {
      // O metodo verifyLogin da class LoginHandler vai verificar e ele retorna 2 coisa
      // true ou false e o token se for true
      $token = LoginHandler::verifyLogin($email, $password);
      if($token) {
        $_SESSION['token'] = $token;
        $this->redirect('/');
      } else {
        $_SESSION['flash'] = 'E-mail e/ou Senha não conferem';
        $this->redirect('/login');
      }

    } else {
      $this->redirect('/login');
    }
  }

  public function signup() {
    echo 'cadastro';
  }


}