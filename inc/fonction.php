<?php
//--------------------- FONCTION DEBUG ------------------//
function debug($var, $mode = 1)
{
    echo '<div class="col-md-8 offset-md-2 alert alert-info my-2">';
    $trace = debug_backtrace(); // fonction prédéfinie retournat un tableau ARRAY contenant des informations tel que la ligne et le fichier où est executé la fonction
    //echo '<pre>'; print_r($trace); echo '</pre>';

    $trace = array_shift($trace); // retire un dimension du tableau ARRAY multidimensionnel
    //echo '<pre>'; print_r($trace); echo '</pre>';

    echo "Debug demandé dans le fichier : $trace[file] à la ligne $trace[line] <hr>";

    if($mode === 1) // si le mode est à 1, on execute un print_r
    {
        echo '<pre>'; print_r($var); echo '</pre>';    
    }
    else // sinon dans tout les autre, quelque soit la valeur de $mode, on execute un var_dump
    {
        echo '<pre>'; var_dump($var); echo '</pre>';
    }
    echo '</div>';
}

//---------------------------- FONCTION MEMBRE CONNECTE
function internauteEstConnecte() // cette fonction indique si le membre est connecté
{
    if(isset($_SESSION['membre'])) // si le tableau ARRAY 'membre' dans la session est défini, existe, c'est que l'internaute est bien passé par la page connexion/inscription 
    {
        return true;   
    }
    else // dans tout les autres cas, l'internaute n'est soit pas inscrit, soite n'est pas connecter
    {
        return false;
    }
}

//---------------------------- FONCTION ADMIN CONNECTE 
function internauteEstConnecteEtEstAdmin() // cette fonction permet de savoir si un membre est administrateur du site
{
    if(internauteEstConnecte() && $_SESSION['membre']['statut'] == 1) // si une session 'membre' et que le statut du membre est à 1, c'est un administrateur
    {
        return true;
    }
    else 
    {
        return false;    
    }
}

//--------------- PANIER
function creationDuPanier()
{
    if(!isset($_SESSION['panier'])) // si l'indice panier dans la session n'est pas définie, c'est que l'internaute n'a pas ajouté de produit dans le panier, donc on crée le panier dans la session
    {
        // on crée un tbleau ARRAY pour chaque indice, nous pouvons avoir plusieurs produits dans le panier
        $_SESSION['panier'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();
    }
}

//----------------------------------------------------------------------
function ajouterProduitDansPanier($titre, $id_produit, $quantite, $prix)
{
    creationDuPanier(); // on contrôle si le panier existe ou non dans la session

    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']);
    // array_search() est une fonction prédéfinie qui retourne l'indice à laquelle se trouve l'id_produit dans la session 'panier'

    //echo $position_produit;
    if($position_produit !== false) // si $position_produit est différenrt de false, cela veut dire que le produit a bien été trouvé dans la session 'panier'
    {
        $_SESSION['panier']['quantite'][$position_produit] += $quantite; // on ajoute la quantité à l'indice trouvé sans écraser la quantité précédente
    }
    else // sinon l'id_produit n'est pas dans la session, on stock les données dans les différents tableaux
    {
        // on stock chaque données dans les différents tableaux de la session 'panier'
        $_SESSION['panier']['titre'][] = $titre; // les [] vide permettent de générer des indices numérique pour les données
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
    }
}

//----------------------------------------------------------------
function montantTotal()
{
    $total = 0;
    // la boucle tourne tant qu'il y a d'id_produit dans la session
    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) 
    {   
        $total += $_SESSION['panier']['quantite'][$i]*$_SESSION['panier']['prix'][$i]; // on mutliplie la quantite par le prix pour chaque indice
    }
    return round($total,2); // on retourne le total arrondi
}