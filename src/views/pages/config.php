<?= $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">
  <?= $render('sidebar', ['activeMenu' => 'config']); ?>

  <section class="feed mt-10">
    <h1>Configurações</h1>

    <?php if (!empty($flash)) : ?>
      <div class="flash"><?php echo $flash; ?></div>
    <?php endif; ?>

    <div class="area-form">
      <form class="config-form" action="<?= $base; ?>/config" method="POST" enctype="multipart/form-data">

        <label>
          Novo Avatar:<br />
          <input type="file" name="avatar" /><br />
          <img class="image-edit" src="<?= $base; ?>/media/avatars/<?= $user->avatar; ?>" />
        </label>

        <label>
          Nova Capa:<br />
          <input type="file" name="cover" /><br />
          <img class="image-edit" src="<?= $base; ?>/media/covers/<?= $user->cover; ?>" />
        </label>

        <hr>

        <label for="">
          Nome Completo:
          <input type="text" name="name" value="<?= $user->name; ?>">
        </label>

        <label for="">
          Data de nascimento:
          <input type="text" name="birthdate" id="birthdate" value="<?= date('d/m/Y', strtotime($user->birthdate)); ?>">
        </label>

        <label for="">
          E-mail:
          <input type="email" name="email" value="<?= $user->email; ?>">
        </label>

        <label for="">
          Cidade:
          <input type="text" name="city" value="<?= $user->city; ?>">
        </label>

        <label for="">
          Trabalho:
          <input type="text" name="work" value="<?= $user->work; ?>">
        </label>
        <hr>

        <label for="">
          Nova Senha:
          <input type="password" name="password">
        </label>

        <label for="">
          Confirmar Nova Senha:
          <input type="password" name="password_confirm">
        </label> <br>
        
        <button class="button">Salvar</button>
      </form>
    </div>

  </section>
</section>

<script src="https://unpkg.com/imask"></script>
<script>
  // colocar um mascara no campo de nascimento
  IMask(
    document.getElementById('birthdate'), {
      mask: '00/00/0000'
    }
  );
</script>
<?= $render('footer'); ?>