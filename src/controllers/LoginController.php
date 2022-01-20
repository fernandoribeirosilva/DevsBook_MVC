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
    
    $this->render('signin', [
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
    // verificar se tem mensagem de flash(error)
    $flash = '';
    if(!empty($_SESSION['flash'])) {
      $flash = $_SESSION['flash'];
      $_SESSION['flash'] = '';// apaga a mensagem 
    }
    
    $this->render('signup', [
      'flash' => $flash
    ]);
  }
  
  // verifica se todos os dados estao prenchidos 
  public function signupAction() {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');
    $birthdate = filter_input(INPUT_POST, 'birthdate');
    
    if($name && $email && $password && $birthdate) {
      
      $birthdate = explode('/', $birthdate);// dividir onde tem uma barra
      if(count($birthdate) != 3) {
        $_SESSION['flash'] = 'Data de nascimento inválida!';
        $this->redirect('/cadastro');
      }

      // invertendo a ordem de dia-mes-ano para ano-mes-dia
      $birthadate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
      
      // verificando se a data e valida
      if(strtotime($birthadate) === false) {
        $_SESSION['flash'] = 'Data de nascimento inválida!';
        $this->redirect('/cadastro');
      }

      // verificar se tem algum usuario com o mesmo e-amil
      if(LoginHandler::emailExists($email) === false) {
        // ja verifica os dados e retorna o token
        $token = LoginHandler::addUser($name, $email, $birthdate, $password);
        $_SESSION['token'] = $token;
        $this->redirect('/');
      } else {
        $_SESSION['flash'] = 'E-mail já cadastrado!';
        $this->redirect('/cadastro');
      }
      
    } else {
      $this->redirect('/cadastro');
    }
    
  }
  
}