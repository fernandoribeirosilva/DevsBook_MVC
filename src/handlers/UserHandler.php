<?php
namespace src\handlers;

use src\models\User;
use src\models\UserRelation;
use src\handlers\PostHandler;

class UserHandler {

  public static function checkLogin() {
    if(!empty($_SESSION['token'])) {
      $token = $_SESSION['token'];

      $data = User::select()->where('token', $token)->one();
      if(!empty($data)) {

        $loggedUser = new User();
        $loggedUser->id = $data['id'];
        $loggedUser->name = $data['name'];  
        $loggedUser->avatar = $data['avatar'];

        return $loggedUser;
      }
    }
    return false;
  }

  public static function verifyLogin($email, $password) {
    $user = User::select()->where('email', $email)->one();

    if($user) {
      if(password_verify($password, $user['password'])) {
        $token = md5(time().rand(0, 9999).time());// gerando o token

        User::update()
          ->set('token', $token)
          ->where('email', $email)
        ->execute();

        return $token;
      }
    }

    return false;// se não encontra
  }

  public static function idExists($id) {
    $user = User::select()->where('id', $id)->one();
    return $user ? true : false;
  }

  public static function emailExists($email) {
    $user = User::select()->where('email', $email)->one();
    return $user ? true : false;
  }

  // se for mandado o segundo parametro $full, que diser ele todas as informações da pessoa
  public static function getUser($id, $full = false) {
    $data = User::select()->where('id', $id)->one();

    if($data) {
      $user = new User();
      $user->id = $data['id'];
      $user->name = $data['name'];
      $user->birthdate = $data['birthdate'];
      $user->city = $data['city'];
      $user->work = $data['work'];
      $user->avatar = $data['avatar'];
      $user->cover = $data['cover'];

      if($full) {
        $user->followers = [];
        $user->following = [];
        $user->photos = [];

        // followers
        $followers = UserRelation::select()->where('user_to', $id)->get();
        foreach ($followers as $follower) {
          $userData = User::select()->where('id', $follower['user_from'])->one();

          $newUser = new User();
          $newUser->id = $userData['id'];
          $newUser->name = $userData['name'];
          $newUser->avatar = $userData['avatar'];

          $user->followers[] = $newUser;
        }

        // following
        $following = UserRelation::select()->where('user_from', $id)->get();//user_from, sou é
        foreach ($following as $follower) {
          $userData = User::select()->where('id', $follower['user_to'])->one();//user_to, que eu sigo

          $newUser = new User();
          $newUser->id = $userData['id'];
          $newUser->name = $userData['name'];
          $newUser->avatar = $userData['avatar'];

          $user->following[] = $newUser;
        }

        // photos
        $user->photos = PostHandler::getPhotosFrom($id);

      }

      return $user;
    }

    return false;
  }

  public static function addUser($name, $email, $birthdate, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = md5(time().rand(0, 9999).time());// gerando o token para logar
    $uname = ucwords($name);

    User::insert([
      'email' => $email,
      'password' => $hash,
      'name' => $uname,
      'birthdate' => $birthdate,
      'token' => $token
    ])->execute();

    return $token;
  }

  public static function isFollowing($from, $to) {
    $data = UserRelation::select()
      ->where('user_from', $from)// from -> eu   
      ->where('user_to', $to)// to -> le 
    ->one();

    if ($data) {
      return true;
    }
    return false;
  }

  public static function follow($from, $to) {
    UserRelation::insert([
      'user_from' => $from,
      'user_to' => $to
    ])->execute();
  }

  public static function unfollow($from, $to) {
    UserRelation::delete()
      ->where('user_from', $from)
      ->where('user_to', $to)
    ->execute();
  }

  public static function searchUser($term) {
    $users = [];

    $data = User::select()->where('name', 'like', '%'.$term.'%')->get();

    if ($data) {
      foreach ($data as $user) {

        $newUser = new User();
        $newUser->id = $user['id'];
        $newUser->name = $user['name'];
        $newUser->avatar = $user['avatar'];

        $users[] = $newUser;

      }
    }

    return $users;
  }
}