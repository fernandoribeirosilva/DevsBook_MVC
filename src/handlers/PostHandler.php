<?php
namespace src\handlers;

use \src\models\Post;

class PostHandler {

  public static function addPost($idUser, $type, $body) {
    $body = trim($body);// limpar os espacos

    if(!empty($idUser) && !empty($body)) {// verificar se o id esta preenchido
      Post::insert([
        'id_user' => $idUser,
        'type' => $type,
        'created_at' => date('Y-m-d H:i:s'),// data atual
        "body" => $body
      ])->execute();
    }
  }
}