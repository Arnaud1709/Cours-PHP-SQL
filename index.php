<?php
    require_once('db.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>
        Stagiaire
    </h1>
    <p><a href="edit.php"> Ajouter </a></p>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom/Prénom</th>
            <th>CP/Ville</th>
            <th>Date d'entrée</th>
            <th>Modifier/Supprimer</th>
        </tr>
        <?php
            //Préparation de la requête
            $sql = 'SELECT id, nom, prenom, cp, ville, date_entree FROM stagiaire';
            $sth = $dbh->prepare($sql);

            //Execute
            $sth->execute();

            //On récupère l'extraction
            $datas = $sth->fetchAll(PDO::FETCH_ASSOC);

            //Gestion des formats desdate en français
            $intlDateFormatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::NONE);

            //On parcours le resulat et imprime à l'écran les données
            //Pour parcourir toute les ligne son fait une boucle
            foreach( $datas as $data){
                echo'<tr>';
                    echo'<td>'.$data['id'].'</td>';
                    echo'<td>'.$data['nom'].' '.$data['prenom'].'</td>';
                    echo'<td>'.$data['cp'].' '.$data['ville'].'</td>';
                    echo'<td>'.$intlDateFormatter->format(strtotime($data['date_entree'])).'</td>';
                    echo '<td><a href="edit.php?edit=1&id='.$data['id'].'">Modifier</a> <a href="delete.php?id='.$data['id'].'">Supprimer</a></td>';
                echo '</tr>';
            }

        ?>
    </table>
    <?php
    // Si le nombre d'éléménent dans le tableau est nul
    // Alors tableau vide donc pas d'enregistrement
        if(count($datas) === 0){
            echo'<p> Ancun stagiaire </p>';
        }
    ?>
</body>
</html>