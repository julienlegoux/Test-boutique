<?php
require_once '../inc/init.php';

//----------- VERIF ADMIN
if(!internauteEstConnecteEtEstAdmin()) // si l'internaute n'est pas admin, il n'a rien à faire ici, on le redirige vers la page connexion
{
    header("location:" . URL . "connexion.php");
}

//----------- SUPPRESSION PRODUIT
//-- on ne rentre dans la condition IF seulement dans le cas où l'on clique sur le lien suppression, c'est à dires qu'on a envoyé action=suppression dans l'URL
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    // permet de selectionner la référence du produit supprimé 
    $ref = $bdd->prepare("SELECT reference FROM produit WHERE id_produit = :id_produit");
    $ref->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $ref->execute();
    $reference = $ref->fetch(PDO::FETCH_ASSOC);
    //----------------------------------------------------------------------------------

    // Réaliser le traitement permettant de supprimer un produit (requête prépérée)
    $resultat = $bdd->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT); // on récupère l'id_produit envoyé dans l'URL
    $resultat->execute();

    $_GET['action'] = 'affichage'; // on modifie l'action dans l'URL afin de revenir sur l'affichage des produits aprés la suppression

    $content .= "<div class='alert alert-success col-md-8 offset-md-2 text-center'>Le produit référence <strong>$reference[reference]</strong> a bien été supprimé!!</div>";
}

//----------- ENREGISTREMENT PRODUIT
if(isset($_POST['form_produit']))
{
    $photo_bdd = '';
    if(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        $photo_bdd = $_POST['photo_actuelle']; // si on souhaite conserver la même photo en cas de modification, on affecte la valeur du champs 'hidden', c'est à dire l'URL de la photo selectionnée en BDD
    }

    //$_POST['reference'] = '';
    if(!empty($_FILES['photo']['name']))
    {
        if(isset($_GET['action']) && $_GET['action'] == 'modification')
        {
            $resultat = $bdd->prepare("SELECT reference FROM produit WHERE id_produit = :id_produit");
            $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT); // on récupère l'id_produit envoyé dans l'URL
            $resultat->execute();
            $produit = $resultat->fetch(PDO::FETCH_ASSOC);
            $nom_photo = $produit['reference'] . '-' . $_FILES['photo']['name'];
        }
        else
        {
            $nom_photo = $_POST['reference'] . '-' . $_FILES['photo']['name']; // on concatène la référence saisie dans le formulaire avec le nom de la photo via la superglobale $_FILES
            //echo $nom_photo;
        }
        
        $photo_bdd = URL . "photo/$nom_photo"; // on définit l'URL de la photo que l'on conservera dans la BDD
        //echo $photo_bdd;

        $photo_dossier = RACINE_SITE . "photo/$nom_photo"; // on définit le chemin physique de la photo sur le serveur
        //echo $photo_dossier;
        
        copy($_FILES['photo']['tmp_name'], $photo_dossier); // la fonction copy() permet de copier la photo directement dans le dossier photo - 2 arguments : 1 - le nom temporaire de la photo -- 2 - le chemin physique de la photo sur le serveur 
    }

    //--------------------- INSERTION PRODUIT
    // Exo : réaliser le script permettant de contrôler la disponibilité de la référence, et si il n'y a pas d'erreur, réaliser le script permettant d'insérer un produit dans la BDD (requête préparée)
    
    $erreur = '';
    if(isset($_GET['action']) && $_GET['action'] == 'ajout')
    {
       
        $verif_ref = $bdd->prepare("SELECT * FROM produit WHERE reference = :reference");
        $verif_ref->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
        $verif_ref->execute();

        if($verif_ref->rowCount() > 0)
        {
            $erreur .= '<div class="col-md-6 offset-md-3 alert alert-danger text-center">Référence existante!! merci de saisir une référence valide!!</div>';  
        }

        if(empty($erreur))
        {
            $insert_produit = $bdd->prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock)");
        }
    }

    if(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        $insert_produit = $bdd->prepare("UPDATE produit SET categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_produit = $_POST[id_produit]");
    }    

    if(empty($erreur))
    {
        // $resultat->bindValue(":reference", $_POST["reference"], PDO::PARAM_STR);
        // $resultat->bindValue(":categorie", $_POST["categorie"], PDO::PARAM_STR);
        // $resultat->bindValue(":titre", $_POST["titre"], PDO::PARAM_STR);
        // $resultat->bindValue(":description", $_POST["description"], PDO::PARAM_STR);
        // $resultat->bindValue(":couleur", $_POST["couleur"], PDO::PARAM_STR);
        // $resultat->bindValue(":taille", $_POST["taille"], PDO::PARAM_STR);
        // $resultat->bindValue(":public", $_POST["public"], PDO::PARAM_STR);
        // $resultat->bindValue(":photo", $photo_bdd, PDO::PARAM_STR);
        // $resultat->bindValue(":prix", $_POST["prix"], PDO::PARAM_INT);
        // $resultat->bindValue(":stock", $_POST["stock"], PDO::PARAM_INT);

        $resultat = $bdd->query("SELECT * FROM produit");

        for($i = 0; $i < $resultat->columnCount(); $i++) // on boucle tant qu'il y a des colonnes dans le résultat
        {
            $colonne = $resultat->getColumnMeta($i); // permet de récolter les informations des champs et des colonnes
            //debug($colonne); 
            if($colonne['name'] != 'id_produit') // on exclu 'id_produit', on ne crée pas de bindValue pour l'id_produit
            {       
                if($colonne['native_type'] == 'LONG') // si le marqueur est de type 'INT' on entre dans le IF et on modifie les arguments de bindValue()
                {
                    $insert_produit->bindValue(":$colonne[name]", $_POST["$colonne[name]"], PDO::PARAM_INT);   
                }
                else // sinon dans tout les autre cas, on crée un bindValue type 'STRING'
                {
                    // en cas de modifiaction, on excku la référence puisque c'est une clé unique dans la BDD, sinon cela génère une erreur SQL
                    if(isset($_GET['action']) && $_GET['action'] == 'modification')
                    {
                        if($colonne['name'] != 'reference') // on exclu la référence en cas de modification
                        {
                            if($colonne['name'] == 'photo')
                            {
                                $insert_produit->bindValue(":$colonne[name]", $photo_bdd, PDO::PARAM_STR);
                            }
                            else
                            {
                                $insert_produit->bindValue(":$colonne[name]", $_POST["$colonne[name]"], PDO::PARAM_STR);
                            }
                        }
                    }
                    else // on entre dans la condition else en cas d'insertion, et on génère des bindValue avec la référence
                    {
                        if($colonne['name'] == 'photo')
                        {
                            $insert_produit->bindValue(":$colonne[name]", $photo_bdd, PDO::PARAM_STR);
                        }
                        else
                        {
                            $insert_produit->bindValue(":$colonne[name]", $_POST["$colonne[name]"], PDO::PARAM_STR);
                        }
                    }
                }                 
            }
        }

        $insert_produit->execute(); // execution des requêtes
        
        //----- REDIRECTION ET MESSAGE DE VALIDATION
        // on redirige vers la boutique en cas de modification avec un message de validation 
        if(isset($_GET['action']) && $_GET['action'] == 'modification')
        {
            $resultat = $bdd->prepare("SELECT reference FROM produit WHERE id_produit = :id_produit");
            $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT); // on récupère l'id_produit envoyé dans l'URL
            $resultat->execute();
            $produit = $resultat->fetch(PDO::FETCH_ASSOC);
            
            $_GET['action'] = 'affichage'; // on modifie l'action dans l'URL qui permet de redirigé vers l'affichage des produits

            $content .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>Le produit reférence <strong>$produit[reference]</strong> a bien été modifié!!</div>"; 
        }
        else // sinon c'est une insertion   
        {
            $_GET['action'] = 'affichage'; // on modifie l'action dans l'URL qui permet de redirigé vers l'affichage des produits

            $content .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>Le produit reférence <strong>$_POST[reference]</strong> a bien été enregistré!!</div>"; 
        }
    }
    $content .= $erreur;
}

//------------- AFFICHAGE PRODUIT
if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{
    // Exercice : afficher l'ensemble de la table produit sous forme de tableau HTML, prévoir un lien modification et suppression pour chaque produit
    // Faites en sorte d'afficher le nombre de produit dans la boutique

    $resultat = $bdd->query("SELECT * FROM produit");

    $content .= '<div class="col-md-10 offset-md-1 text-center">
                    <h2 class="text-center mt-4">Affichage Produit(s)</h2>
                    <p>Nombre de produit(s) dans la boutique : <strong>' . $resultat->rowCount() . '</strong></p>
                </div>';    

    $content .= '<table class="table"><tr>';
    for($i = 0; $i < $resultat->columnCount(); $i++)
    {   
        $colonne = $resultat->getColumnMeta($i);
        //debug($colonne);
        if($colonne['name'] != 'id_produit')
        {
            $content .= '<th>' . strtoupper($colonne['name']) . '</th>';    
        }
    }
    $content .= '<th>MODIFICATION</th>';
    $content .= '<th>SUPPRESSION</th>';
    $content .= '</tr>';
    while($produit = $resultat->fetch(PDO::FETCH_ASSOC))
    {
        $content .= '<tr>';
        //debug($produit);
        foreach($produit as $key => $value)
        {
            if($key != 'id_produit')
            {
                if($key == 'photo')
                {
                    $content .= "<td><img src='$value' alt='$key' width='70' height='70' style='border: 1px solid black;'></td>";    
                }
                else
                {
                    if($key == 'prix')
                    {
                        $content .= "<td>$value €</td>"; 
                    }
                    else
                    {
                        $content .= "<td>$value</td>";
                    }
                }   
            }
        }
        $content .= '<td class="text-center"><a href="?action=modification&id_produit=' . $produit['id_produit'] . '"><i class="fas fa-edit"></i></a></td>';
        $content .= '<td class="text-center"><a href="?action=suppression&id_produit=' . $produit['id_produit'] . '" onClick="return(confirm(\'En êtes vous certain ?\'));"><i class="fas fa-trash-alt"></i></a></td>';
        $content .= '</tr>';
    }
    $content .= '</table>';

}




require_once '../inc/header.php';
// debug($_POST);
// debug($_FILES);
?>

<!-- LIENS PRODUITS -->
<div class="list-group col-md-6 offset-md-3 text-center mt-4">
    <p class="list-group-item list-group-item-dark">
        <strong>BACK OFFICE</strong>
    </p>
    <a href="?action=affichage" class="list-group-item list-group-item-action">Affichage produits</a>
    <a href="?action=ajout" class="list-group-item list-group-item-action">Ajout produit</a><hr> 
</div><hr> 

<?= $content ?>

<?php 
if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')): 
   
    if(isset($_GET['id_produit']))
    {
        $resultat = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
        $resultat->execute();

        $produit_modif = $resultat->fetch(PDO::FETCH_ASSOC);
        //debug($produit_modif);
    }


    // Si la référence du produit existe bien dans la BDD, on l'affiche sinon on affiche une chaine de caractères vide
    $id_produit = (isset($produit_modif['id_produit'])) ? $produit_modif['id_produit'] : '';
    $reference = (isset($produit_modif['reference'])) ? $produit_modif['reference'] : '';
    $categorie = (isset($produit_modif['categorie'])) ? $produit_modif['categorie'] : '';
    $titre = (isset($produit_modif['titre'])) ? $produit_modif['titre'] : '';
    $description = (isset($produit_modif['description'])) ? $produit_modif['description'] : '';
    $couleur = (isset($produit_modif['couleur'])) ? $produit_modif['couleur'] : '';
    $taille = (isset($produit_modif['taille'])) ? $produit_modif['taille'] : '';
    $public = (isset($produit_modif['public'])) ? $produit_modif['public'] : '';
    $photo = (isset($produit_modif['photo'])) ? $produit_modif['photo'] : '';
    $prix = (isset($produit_modif['prix'])) ? $produit_modif['prix'] : '';
    $stock = (isset($produit_modif['stock'])) ? $produit_modif['stock'] : '';
    $controle = (isset($_GET['action']) && $_GET['action'] == 'modification') ? 'disabled' : '';
?>
<!-- Faites en sorte de mettre le champ 'reference' en 'disabled' en cas de modification -->
<h2 class="text-center mt-4"><?= ucfirst($_GET['action']) ?> Bière</h2>

<form method="post" action="" enctype="multipart/form-data" class="col-md-6 offset-md-3">

    <input type="hidden" id="id_produit" name="id_produit" value="<?= $id_produit ?>">

    <div class="form-group">
        <label for="reference">Référence</label>
        <input type="text" class="form-control" id="pseudo" name="reference" placeholder="Enter reference" value="<?= $reference ?>" <?= $controle ?>>
    </div>
    <div class="form-group">
        <label for="categorie">Catégorie</label>
        <input type="text" class="form-control" id="categorie" name="categorie" placeholder="Enter categorie" value="<?= $categorie ?>">
    </div>
    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" class="form-control" id="titre" name="titre" placeholder="Enter titre" value="<?= $titre ?>">
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea type="text" class="form-control" id="description" name="description" placeholder="Saisissez votre description..."><?= $description ?></textarea>
    </div>
    <div class="form-group">
        <label for="couleur">Couleur</label>
        <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Enter couleur" value="<?= $couleur ?>">
    </div>
    <div class="form-group">
        <label for="taille">Taille</label>
        <select class="form-control" id="taille" name="taille">
            <option value="demi" <?php if($taille == 'demi') echo 'selected' ?>>Demi</option>
            <option value="pinte" <?php if($taille == 'pinte') echo 'selected' ?>>Pinte</option>
            <option value="girafe" <?php if($taille == 'girafe') echo 'selected' ?>>Girafe</option>
            <option value="pack" <?php if($taille == 'pack') echo 'selected' ?>>Pack</option>
        </select>
    </div>
    <div class="form-group">
        <label for="public">Public</label>
        <select class="form-control" id="public" name="public">
            <option value="m" <?php if($public == 'm') echo 'selected' ?>>Homme</option>
            <option value="f" <?php if($public == 'f') echo 'selected' ?>>Femme</option>
            <option value="mixte" <?php if($public == 'mixte') echo 'selected' ?>>Mixte</option>
        </select>
    </div>
    <div class="form-group">
        <label for="photo">Photo</label>
        <input type="file" class="form-control" id="photo" name="photo">
        <input type="hidden" id="photo_actuelle" name="photo_actuelle" value="<?= $photo ?>">
    </div>
    <?php if(!empty($photo)): ?>
        <em>Vous pouvez uploader une nouvelle photo si vous souhaiter la changer</em>
        <img src="<?= $photo ?>" width="120" alt="" class="m-2" style="border: 1px solid black;"> 
    <?php endif; ?>
    <div class="form-group">
        <label for="prix">Prix</label>
        <input type="text" class="form-control" id="prix" name="prix" placeholder="Enter prix" value="<?= $prix ?>">
    </div>
    
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="text" class="form-control" id="stock" name="stock" placeholder="Enter stock" value="<?= $stock ?>">
    </div>
    
    <button type="submit" name="form_produit" value="valid_produit" class="btn btn-dark col-md-12 mb-4"><?= ucfirst($_GET['action']) ?> Bière</button>
</form>

<?php
endif;

require_once '../inc/footer.php';