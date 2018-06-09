<?php
require_once 'inc/init.php';
require_once 'inc/header.php';

if(!internauteEstConnecte()) // si l'internaute n'est pas connecté, il n'y a rien à faire sur la page profil, on le redirige vers la page connexion
{
    header("location:connexion.php");
}

//debug($_SESSION);
?>
<!-- strtoupper() permet de mettre en majuscule une chaine de caractères -->
<!-- Tenter d'afficher Bonjour suivi du pseudo de l'internaute connecté -->
<table class="table table-dark my-5"><tr>
    <th class="text-center" colspan="2"><h2>Bonjour <strong class="text-info"><?= strtoupper($_SESSION['membre']['pseudo']) ?></strong></h2></th></tr>
    <?php foreach($_SESSION['membre'] as $key => $value): ?>

        <?php if($key != 'statut' && $key != 'id_membre'): ?>

            <tr class="text-center"><th><?= strtoupper($key) ?></th><td><?= $value ?></td></tr>

        <?php endif; ?>

    <?php endforeach; ?>
    <tr><td colspan="2" class="text-center"><a href="#" class="alert-link text-info">Modifier votre compte</a></td></tr>
</table>
<?php
require_once 'inc/footer.php';