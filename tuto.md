

Créer un autre fichier php pour executer l'accès à la base de donnée
<?php
define('DATABASE', 'livecoding');
define('USER', 'root');
define('PWD', '');
define('HOST', 'localhost');

    try {
            $dbh = new PDO('mysql:host='.HOST.';dbname='. DATABASE, USER, PWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }

Lier la database au PHP index
<?php
    require_once('db.php');
?>

Préparer une requête sql
On intègre en avance une variable que l'on nomme $sql

$sql = 'SELECT id, nom, prenom, cp, ville, date_entree FROM stagiaire';
$sth = $dbh->prepare($sql);
$sth->execute();

On demande à $sth de stocker temporairement la requête $sql à qui on a affecté la variable $dbh
La flèche -> permet de réaliser une méthode
On l'execute ensuite avec la commande execute

On récupère ensuite l'extraction avec fetchALL
$datas = $sth->fetchAll(PDO::FETCH_ASSOC);
On donne a $datas un tableau associatif via PDO::FETCH_ASSOC (cf php objet à étudier plus tard)

On parcours le resulat et imprime à l'écran les données
    Pour parcourir toute les ligne son fait une boucle
    foreach( $datas as $data){
        echo'<tr>';
            echo'<td>'.$data['id'].'</td>';
            echo'<td>'.$data['nom'].' '.$data['prenom'].'</td>';
            echo'<td>'.$data['cp'].' '.$data['ville'].'</td>';
            echo'<td>'.$data['date_entree'].'</td>';
        echo '</tr>';

        Ici on nomme les éléments du tableau $data car ils sont une partie de l'ensemble de données $datas