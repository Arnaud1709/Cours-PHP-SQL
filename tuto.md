# Tutoriel: Créer un tableau en PHP/SQL et y extraire des valeurs
## Comment créer un tableau?
### La structure en PHP

Nous commençons par donner un nom au tableau, ici il sera écrit dans un '''<h1>'''. Je créé une balise '''<table>''' dans '''index.php''' qui contiendra des balises '''<th>''' où seront écrit le nom de chaque colone.

    <h1>
        Stagiaire
    </h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom/Prénom</th>
            <th>CP/Ville</th>
            <th>Date d'entrée</th>
            <th>Modifier/Supprimer</th>
        </tr>
    </table>

Afin de remplir ce tableau via la base de données du serveur, il faut donner l'accès au tableau. Pour une question d'organisation, nous allons créer un autre fichier nommé '''db.php''' (db = data base) pour executer l'accès à la base de donnée

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
    ?>

Ici, nous utilisons des constantes '''define''' afin d'atribuer des valeurs fixe aux paramètres de contexion:
    - '''DATABASE''' demande le chemin de la base de donnée
    - '''USER''' et '''PWD''' contient les paramètres d'accès au serveur
    - '''HOST''' indique le chemin vers le serveur

'''$dbh''' devient donc la variable permettant la connexion au serveur.

La fonction '''try''' est une fonction générique de connexion, cependant, nous rajoutons '''array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')''' à son contenu afin d'éviter les erreurs d'écriture et d'afficher les résultats en '''UTF-8'''

Pour que notre tableau puisse exploiter les données prélevées, nous devons lier '''db.php''' à '''index.php'''

    <?php
        require_once('db.php');
    ?>

Afin d'éviter des erreur ou des bloucles, nous utilision l'instruction '''require_once'''. Tout d'abord '''require''' pour produire une erreur fatale en cas de problèmes (ce qui stoppera le script) et '''_once''' afin de vérifier si le fichier est déjà inclus et si c'est ke cas, ne pas l'inclure une deuxième fois.

La structure du tableau étant terminée, il ne reste qu'a aller chercher les informations qui nous intéressent.

# Extraction de données avec SQL

Pour extraire des données de la '''database''' il faut préparer des '''requêtes sql'''.
Pour gagner en ergonomie, on intègre en avance une variable que l'on nomme $sql

    <?php
        $sql = 'SELECT id, nom, prenom, cp, ville, date_entree FROM stagiaire';
        $sth = $dbh->prepare($sql);
        $sth->execute();
    ?>

Dans '''$sql''' on écrit la requête sql comme elle aurait été écrite sur '''phpMyAdmin'''
La flèche '''->''' permet de réaliser une méthode, ici avec l'instruction '''prepare''' on demande à '''$sth''' de stocker temporairement la requête '''$dbh''' à qui on a affecté la variable '''$sql'''
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

Mettre la date au format français