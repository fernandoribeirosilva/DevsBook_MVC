<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

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

  public static function getHomeFeed($idUser, $page) {
    $perPage = 2;
    // 1. pegar lista de usuários que EU sigo.
    // user_from sou eu, e user_to é a pessoa que estou seguindo
    $userList = UserRelation::select()->where('user_from', $idUser)->get();
    $users = [];
    foreach($userList as $userItem) {
      $users[] = $userItem['user_to'];// user_to lista das pessoa que estou seguindo
    }
    $users[] = $idUser;// colocar o usuario logado para ele ver as suas postagem támbem

    // 2. pegar os post dessa galera ordenado pela data.
    $postList = Post::select()
      ->where('id_user', 'in', $users)
      ->orderBy('created_at', 'desc')
      ->page($page, $perPage)
    ->get();

    // esta condição retona quantos post tem
    $total = Post::select()
      ->where('id_user', 'in', $users)
    ->count();
    $pageCount = ceil($total / $perPage);

    // 3. transformar o resultado em objetos do models
    $posts = [];
    foreach($postList as $postItem) {
      $newPost = new Post();
      $newPost->id = $postItem['id'];
      $newPost->type = $postItem['type'];
      $newPost->created_at = $postItem['created_at'];
      $newPost->body = $postItem['body'];
      $newPost->mine = false;

      // verificando se o post é da pessoa que esta logado
      if($postItem['id_user'] == $idUser) {
        $newPost->mine = true;
      }

      // 4. preencher as informações adicionais no post
      $newUser = User::select()->where('id', $postItem['id_user'])->one();
      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      // TODO: 4.1 preencher informações LIKE
      $newPost->likeCount = 0;
      $newPost->liked = false;

      // TODO: 4.2 preencher informações de COMMETS
      $newPost->comments = [];

      $posts[] = $newPost;
    }
    // 5. retornar o resultado.
    return [
     'posts' => $posts,
     'pageCount' => $pageCount,
     'currentPage' => $page// pagina atual
    ];
  }
}