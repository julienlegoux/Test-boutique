<?php
require_once 'inc/init.php';
require_once 'inc/header.php';
?>


<!-- Page Content -->
<div class="container">

<div class="row">

  <div class="col-lg-3">

    <h1 class="my-4">Catégories</h1>
    <div class="list-group">
    <?php
    $resultat = $bdd->query("SELECT DISTINCT categorie FROM produit");
    while($categorie = $resultat->fetch(PDO::FETCH_ASSOC)):
    ?>
      <a href="?categorie=<?= $categorie['categorie'] ?>" class="list-group-item"><?= $categorie['categorie'] ?></a>
    <?php endwhile; ?>  
    </div>

  </div>
  <!-- /.col-lg-3 -->

  <div class="col-lg-9">

    <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="carousel-item active">
          <img class="d-block img-fluid" src="http://localhost/PHP/10 - boutique/photo/biere-1.jpg" alt="First slide">
        </div>
        <div class="carousel-item">
          <img class="d-block img-fluid" src="http://localhost/PHP/10 - boutique/photo/biere-2.jpg" alt="Second slide">
        </div>
        <div class="carousel-item">
          <img class="d-block img-fluid" src="http://localhost/PHP/10 - boutique/photo/biere3.jpg" alt="Third slide">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

    <div class="row">

    <?php
    if(isset($_GET['categorie'])):
        $donnees = $bdd->prepare("SELECT * FROM produit WHERE categorie = :categorie");
        $donnees->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_STR);
        $donnees->execute();

        while($produit = $donnees->fetch(PDO::FETCH_ASSOC)):
        //debug($produit);
    ?>
       
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <a href="fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>"><img class="card-img-top" src="<?= $produit['photo'] ?>" alt=""></a>
                <div class="card-body">
                    <h4 class="card-title">
                    <a href="fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>" class="text-dark"><?= $produit['titre'] ?></a>
                    </h4>
                    <h5><?= $produit['prix'] ?> €</h5>
                    <p class="card-text"><?= $produit['description'] ?></p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                </div>
            </div>
        </div>

    <?php
        endwhile; 
    
    else:

        $donnees = $bdd->prepare("SELECT * FROM produit");
        $donnees->execute();

        while($produit = $donnees->fetch(PDO::FETCH_ASSOC)):
    ?>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <a href="fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>"><img class="card-img-top" src="<?= $produit['photo'] ?>" alt=""></a>
            <div class="card-body">
            <h4 class="card-title">
                <a href="fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>" class="text-dark"><?= $produit['titre'] ?></a>
            </h4>
            <h5><?= $produit['prix'] ?> €</h5>
            <p class="card-text"><?= $produit['description'] ?></p>
            </div>
            <div class="card-footer">
            <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
            </div>
        </div>
    </div>

    <?php  
        endwhile;  
    endif; 
    ?>
    </div>
    <!-- /.row -->

  </div>
  <!-- /.col-lg-9 -->

</div>
<!-- /.row -->

</div>
<!-- /.container -->

<?php
require_once 'inc/footer.php';