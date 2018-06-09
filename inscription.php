<?php
require_once 'inc/init.php';

if(internauteEstConnecte()) // si l'internaute est connecté, il n'a rien à faire sur la page 'connexion', on le redirige vers sa page profil 
{
    header("location:profil.php");
}

/*
    Contrôler les champs suivants : 
     - faites en sorte d'informer l'internaute si le pseudo et l'email sont déja existants en BDD
     - contrôler que les 2 mots de passe sont identiques
     - contrôler la validité du champs email
     - contrôler que le pseudo soit compris entre 2 et 20 caractères 
*/
if(isset($_POST['form_inscription']))
{
    $erreur = '';
    //----------- VERIF PSEUDO
    $verif_pseudo = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $verif_pseudo->execute();
    if($verif_pseudo->rowCount() > 0)
    {
        $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Pseudo existant! Merci de saisir à nouveau</div>';
    }
    else
    {
        if((strlen($_POST['pseudo']) < 2 || strlen($_POST['pseudo']) > 20) || !preg_match('#^[A-Za-z0-9._-]+$#', $_POST['pseudo']))
        {
            $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Pseudo : Taille (entre 2 et 20 caractères) ou format non valide (caractères autorisés : A-Za-z0-9._-) !!</div>';
        }


        /*
            preg_match() : une expression régulière (regex) est toujours entouré de dieze afin de préciser des options: 
            - ^ indique le début de la chaine 
            - $ indique la fin de la chaine
            - + est le pour dire que les caractères autorisés peuvent apparaitre plusieurs fois    
        */
    }

    //----------- VERIF EMAIL
    // si le format EMAIL est erronné, on entre dans la condition IF
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
    {
        $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Format EMAIL erroné!</div>';
    }
    else // sinon le format est valide, on contrôle dans le else l'existence du email en BDD
    {
        $verif_email = $bdd->prepare("SELECT * FROM membre WHERE email = :email");
        $verif_email->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $verif_email->execute();
        if($verif_email->rowCount() > 0)
        {
            $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Email existant! <a href="connexion.php" class="alert-link">Connectez-vous</a> ou vous vérifier vos identifiants!</div>';
        }
    }

    //---------- VERIF MDP
    if($_POST['mdp'] !== $_POST['mdp_confirm'])
    {
        $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Vérifier le confirmation mot de passe!!</div>';   
    }

    $content .= $erreur;

    // Réaliser le traitement permettant d'insérer un membre dans la table 'membre' si il n'y a pas d'erreur (requete préparée)
    if(empty($erreur))
    {
        // contrôle faille XSS :
        // trim() est une fonction prédéfinie qui supprime les espaces en début et fin de chaine 
        foreach($_POST as $indice => $valeur)
        {
            $_POST[$indice] = strip_tags(trim($valeur)); 
        }

        $insert = $bdd->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse)");

        //Les mot de passe ne sont jamais gardés en clair dans la BDD
        // password_hasch() est un efonction prédéfinie permettant de crée une clé de hachage
        // Pour comparer un e clé de hachage avec une chaine de caractère, au moment de la connexion, nous utiliserons password_verify()
        //$_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT); 

        $resultat = $bdd->query("SELECT * FROM membre");

        for($i = 0; $i < $resultat->columnCount(); $i++) // on boucle tant qu'il y a des colonnes dans le résultat
        {
            $colonne = $resultat->getColumnMeta($i); // permet de récolter les informations des champs et des colonnes
            //debug($colonne); 
            if($colonne['name'] != 'id_membre' && $colonne['name'] != 'statut') // on exclu 'id_membre' et 'statut'
            {       
                if($colonne['native_type'] == 'LONG') // si le marqueur est de type 'INT' on entre dans le IF et on modifie les arguments de bindValue()
                {
                     $insert->bindValue(":$colonne[name]", $_POST["$colonne[name]"], PDO::PARAM_INT);   
                }
                else // sinon dans tout les autre cas, types 'STRING'
                {
                    $insert->bindValue(":$colonne[name]", $_POST["$colonne[name]"], PDO::PARAM_STR);
                }                 
            }
        }

        $insert->execute();

        $content .= '<div class="col-md-6 offset-md-3 alert alert-success text-center">Vous êtes inscrit sur le site, vous pouvez dès à present vous <a href="connexion.php" class="alert-link">connecter!!</a></div>';  
    }

}

require_once 'inc/header.php';
//debug($_POST);
?>

<!-- Réaliser un formulaire HTML correspondant à la table 'membre' de la BDD 'boutique' (sauf les champs id_membre, status) -->

<h2 class="text-center mt-4">Inscription</h2>
<hr>
<?= $content ?>
<form method="post" action="" class="col-md-6 offset-md-3">
    <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Enter pseudo">
    </div>
    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="text" class="form-control" id="mdp" name="mdp" placeholder="Enter mdp">
    </div>
    <div class="form-group">
        <label for="mdp_confirm">Confirmer mot de passe</label>
        <input type="text" class="form-control" id="mdp_confirm" name="mdp_confirm" placeholder="Enter confirm mdp">
    </div>
    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" class="form-control" id="nom" name="nom" placeholder="Enter nom">
    </div>
    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Enter prenom">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Enter email">
    </div>
    <div class="form-group">
        <label for="civilite">Civilité</label>
        <select class="form-control" id="civilite" name="civilite">
            <option value="m">Homme</option>
            <option value="f">Femme</option>
        </select>
    </div>
    <div class="form-group">
        <label for="ville">Ville</label>
        <input type="text" class="form-control" id="ville" name="ville" placeholder="Enter ville">
    </div>
    <div class="form-group">
        <label for="code_postal">Code Postal</label>
        <input type="text" class="form-control" id="code_postal" name="code_postal" placeholder="Enter code_postal">
    </div>
    <div class="form-group">
        <label for="adresse">Adresse</label>
        <textarea type="text" class="form-control" id="adresse" name="adresse" placeholder="Saisissez votre adresse..."></textarea>
    </div>
    <button type="submit" name="form_inscription" value="valid_inscription" class="btn btn-dark col-md-12 mb-4">Inscription</button>
</form>

<?php
require_once 'inc/footer.php';