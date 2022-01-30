<?php
namespace src\handlers;

use \src\models\Post;
use src\models\PostComment;
use \src\models\PostLike;
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

  public static function _postListToObject($postList, $loggedeUserId) {

    // 3. transformar o resultado em objetos do models
    $posts = [];
    foreach($postList as $postItem) {
      $newPost = new Post();
      $newPost->id = $postItem['id'];
      $newPost->type = $postItem['type'];
      $newPost->created_at = $postItem['created_at'];
      $newPost->body = $postItem['body'];
      $newPost->mine = false;

      // verificando se o post é da pessoa que esta logado, caso eu queira editar/deletar
      if($postItem['id_user'] == $loggedeUserId) {
        $newPost->mine = true;
      }

      // 4. preencher as informações adicionais no post
      $newUser = User::select()->where('id', $postItem['id_user'])->one();
      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      // TODO: 4.1 preencher informações LIKE
      $likes = PostLike::select()->where('id_post', $postItem['id'])->get();
      
      $newPost->likeCount = count($likes);
      $newPost->liked = self::isLiked($postItem['id'], $loggedeUserId);

      // TODO: 4.2 preencher informações de COMMETS
      $newPost->comments = PostComment::select()->where('id_post', $postItem['id'])->get();
      foreach ($newPost->comments as $key => $comment) {
        $newPost->comments[$key]['user'] = User::select()->where('id', $comment['id_user'])->one();
      }

      $posts[] = $newPost;
    }

    return $posts;
  }

  public static function isLiked($id, $loggedeUserId) {
    $myLike = PostLike::select()
      ->where('id_post', $id)
      ->where('id_user', $loggedeUserId)
    ->get();

    if (count($myLike) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public static function deleteLike($id, $loggedeUserId) {
    PostLike::delete()
      ->where('id_post', $id) 
      ->where('id_user', $loggedeUserId) 
    ->execute();
  }

  public static function addLike($id, $loggedeUserId) {
    PostLike::insert([
      'id_post' => $id,
      'id_user' => $loggedeUserId,
      'created_at' => date('Y-m-d H:i:s')
    ])->execute();
  }

  public static function addComment($id, $txt, $loggedeUserId) {
    PostComment::insert([
      'id_post' => $id,
      'id_user' => $loggedeUserId,
      'created_at' => date('Y-m-d H:i:s'),
      'body' => $txt
    ])->execute();
  }

  public static function getUserFedd($idUser, $page, $loggedeUserId) {
    $perPage = 2;

    // 2. pegar os post do usuario.
    $postList = Post::select()
      ->where('id_user', $idUser)
      ->orderBy('created_at', 'desc')
      ->page($page, $perPage)
    ->get();

    // esta condição retona quantos post tem
    $total = Post::select()
      ->where('id_user', $idUser)
    ->count();
    $pageCount = ceil($total / $perPage);

    // 3. transformar o resultado em objetos do models
    $posts = self::_postListToObject($postList, $loggedeUserId);

    // 5. retornar o resultado.
    return [
     'posts' => $posts,
     'pageCount' => $pageCount,
     'currentPage' => $page// pagina atual
    ];
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
    $posts = self::_postListToObject($postList, $idUser);

    // 5. retornar o resultado.
    return [
     'posts' => $posts,
     'pageCount' => $pageCount,
     'currentPage' => $page// pagina atual
    ];
  }

  public static function getPhotosFrom($idUser) {
    $photosData = Post::select()
      ->where('id_user', $idUser)
      ->where('type', 'photo')
    ->get();

    $photos = [];

    foreach($photosData as $photo) {
      $newPost = new Post();
      $newPost->id = $photo['id'];
      $newPost->type = $photo['type'];
      $newPost->created_at = $photo['created_at'];
      $newPost->body = $photo['body'];// o bosy de uma photo e o link dela

      $photos[] = $newPost;
    }

    return $photos;
  }
}