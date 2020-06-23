<?php
    require_once('db.php');

    //Tester l'existence de la variable d'url

    if (isset( $_GET['id'])){

        //Requête sql de suppression avec marqueur nommé qui sera lié avec une variable
        $sql = 'DELETE FROM stagiaire WHERE id=:id';

        //prépare la requête
        $sth = $dbh->prepare($sql);

        //Lien entre le marqueur nommé et une variable en précisant le type de données de la colonne sql
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);

        //Executer la requête
        $sth->execute();

    }
    
    //Redirection avec PHP  (/!\ L majuscule à Location et espace après les ":")
    header('Location: index.php');

?>