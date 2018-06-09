<?php
require_once 'inc/init.php';

if(isset($_GET['action']) && $_GET['action'] == 'deconnexion') // on ne rentre ici que lorsque l'on clique sur le lien deconnexion
{
    session_destroy(); // on détruit la session
}

if(internauteEstConnecte()) // si l'internaute est connecté, il n'a rien à faire sur la page 'connexion', on le redirige vers sa page profil 
{
    header("location:profil.php");
}

if(isset($_POST['form_connexion']))
{
    $resultat = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo OR email = :email"); // on seletionne en BDD tout les membres qui possède le même pseudo ou Email que l'internaute a saisie
    $resultat->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR); // on associe des valeurs au marqueurs
    $resultat->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);
    $resultat->execute();

    if($resultat->rowCount() > 0) // si le résultat est supérieur à 0, c'est que le pseudo ou l'email est bien connu en BDD
    {
       $membre = $resultat->fetch(PDO::FETCH_ASSOC); // on associe la méthode fetch() au résultat afin de le rendre xploitable et de récupérer les données de l'internaute ayant saisie le bon pseudo ou Email
       //debug($membre);

       //password_verify($_POST['mdp'], $membre['mdp']) password_verify() permet de comparer un clé de hachage avec un mot de passe
       if($membre['mdp'] == $_POST['mdp']) // on contrpole que le mot de passe de la BDD correspond au mot de passe saisie dans le formulaire par l'internaute
       {
            foreach($membre as $indice => $valeur) // on, passe en revue les données du memebre qui a le bon pseudo/email et mdp
            {
                if($indice != 'mdp') // on exclu le mdp qui n'est pas conservé dans la session
                {
                    $_SESSION['membre'][$indice] = $valeur; //on crée dans le fichier SESSION un tableau emebre et on enregistre les données qe l'internaute qui pourra dès à présent naviguer sur le site sans être déconnecté
                }
            }
            //debug($_SESSION);
            header("location:profil.php"); // ayant les bons identifiants, on le redirige vers sa page profil
       }
       else // soinon l'internaute a saisie un mauvais MDP
       {
            $content .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Pseudo ou Email erroné!! <a href="inscription.php" class="alert-link">Inscrivez-vous</a> afin de pouvoir vous connecter ou vérifiez vos identifiants!!</div>';
       }
    }
    else // sinon l'internaute a saisie un mauvais pseudo ou Email
    {
        $content .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Pseudo ou Email erroné!! <a href="inscription.php" class="alert-link">Inscrivez-vous</a> afin de pouvoir vous connecter ou vérifiez vos identifiants!!</div>';
    }
}

// debug($_POST);
require_once 'inc/header.php';
?>

<!-- Réaliser un formulaire HTML de connexion (champs pseudo/email, mot de passe, bouton de validation) -->

<h2 class="text-center mt-4">Connexion</h2>
<hr>
<?= $content ?>
<form method="post" action="" class="col-md-6 offset-md-3">
    <div class="form-group">
        <label for="pseudo">Pseudo ou Email</label>
        <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" placeholder="Enter pseudo ou Email">
    </div>
    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="text" class="form-control" id="mdp" name="mdp" placeholder="Enter mdp">
    </div>
    <button type="submit" name="form_connexion" value="valid_connexion" class="btn btn-dark col-md-12 mb-4">Connexion</button>
</form>    


<?php
require_once 'inc/footer.php';