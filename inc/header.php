<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ma CAVE A BIERES</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= URL ?>inc/css/style.css">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <a class="navbar-brand" href="boutique.php">Ma CAVE A BIERES</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample03">
            <ul class="navbar-nav mr-auto">
                <?php
                if(internauteEstConnecte())
                {   // accés membre du site
                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'profil.php">Profil</a></li>'; 
                    
                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'boutique.php">Boutique</a></li>'; 

                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'panier.php">Panier</a></li>'; 

                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'connexion.php?action=deconnexion">Deconnexion</a></li>'; 
                }
                else // accès visiteur
                { 
                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'inscription.php">Inscription</a></li>'; 

                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'connexion.php">Connexion</a></li>'; 

                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'boutique.php">Boutique</a></li>'; 

                    echo '<li class="nav-item"><a class="nav-link" href="' . URL . 'panier.php">Panier</a></li>'; 
                }
                
                if(internauteEstConnecteEtEstAdmin())
                {
                    echo '<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">BACK OFFICE</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown03">
                        <a class="dropdown-item" href="' . URL . 'admin/gestion_boutique.php">Gestion boutique</a>
                        <a class="dropdown-item" href="' . URL . 'admin/gestion_membre.php">Gestion membre</a>
                        <a class="dropdown-item" href="' . URL . 'admin/gestion_commande.php">Gestion commande</a>
                        </div>
                    </li>';
                }
                ?>
                
                
            </ul>
            <form class="form-inline my-2 my-md-0">
            <input class="form-control" type="text" placeholder="Search">
            </form>
        </div>
    </nav>
    <section class="container mon-conteneur">