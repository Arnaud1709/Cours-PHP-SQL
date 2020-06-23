# Tutoriel: Créer un tableau en PHP/SQL et y extraire des valeurs 

## Comment créer un tableau? 

### La structure en PHP 

Nous commençons par donner un nom au tableau, ici il sera écrit dans un ```<h1>```. Je créé une balise ```<table>``` dans ```index.php``` qui contiendra des balises ```<th>``` où seront écrit le nom de chaque colone.

```
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
    </table>

```

On créé en avance un lien ```Ajouter``` qui permettra d'isérer des valeurs dans le tableau grâce à un formulaire sur la page ```edit.php```
Afin de remplir ce tableau via la base de données du serveur, il faut donner l'accès au tableau. Pour une question d'organisation, nous allons créer un autre fichier nommé ```db.php``` (db = data base) pour executer l'accès à la base de donnée

```
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
```

Ici, nous utilisons des constantes ```define``` afin d'atribuer des valeurs fixe aux paramètres de conexion:
    - ```DATABASE``` demande le chemin de la base de donnée
    - ```USER``` et ```PWD``` contient les paramètres d'accès au serveur
    - ```HOST``` indique le chemin vers le serveur

```$dbh``` (data base handle) devient donc la variable permettant la connexion au serveur.

La fonction ```try``` est une fonction générique de connexion, cependant, nous rajoutons ```array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')``` à son contenu afin d'éviter les erreurs d'écriture et d'afficher les résultats en ```UTF-8```

Pour que notre tableau puisse exploiter les données prélevées, nous devons lier ```db.php``` à ```index.php```

```
    <?php
        require_once('db.php');
    ?>
```

Pour que les fichiers liés à ```db.php``` executent son contenu, nous utiliserons l'instruction ```require_once```. Cette instruction se compose de deux paramètres: ```require``` qui execute la connection, il produit une erreur fatale en cas de problèmes (ce qui stoppera le script) et ```_once``` qui vérifie si le fichier est déjà inclus et si c'est le cas, ne l'inclus pas une deuxième fois.

La structure du tableau étant terminée, il ne reste qu'a aller chercher les informations qui nous intéressent.

### Extraction de données avec SQL

Pour extraire des données de la ```database``` il faut préparer des ```requêtes sql```.
Pour gagner en ergonomie, on intègre en avance une variable que l'on nomme ```$sql```

```
    <?php
        $sql = 'SELECT id, nom, prenom, cp, ville, date_entree FROM stagiaire';
        $sth = $dbh->prepare($sql);
        $sth->execute();
    ?>
```

Dans ```$sql``` on écrit la requête sql comme elle aurait été écrite sur ```phpMyAdmin```
La flèche ```->``` permet de réaliser une méthode, ici avec l'instruction ```prepare``` on demande à ```$sth``` (statement handle) de stocker temporairement la requête ```$dbh``` à qui on a affecté la variable ```$sql```
On l'execute ensuite avec la commande execute

On récupère ensuite l'extraction avec ```fetchALL```

```
    $datas = $sth->fetchAll(PDO::FETCH_ASSOC);
```

On donne a ```$datas``` un tableau associatif via ```PDO::FETCH_ASSOC``` (cf php objet à étudier plus tard)

Avant d'afficher le tableau, il faut corriger l'affichage de la date qui apparaitra sous ```format Américain (yyyy/dd/mm)```

```
    $intlDateFormatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
```

Pour modifier facilement le format de date, on utilise l'extension PHP ```intl``` (à installer sur Vagrant ou à cocher sur Wamp)
Les trois premières valeur de ```IntlDateFormatter``` doivent être obligatoirement renseignées : ```('choisir la langue', 'indiquer l'affichage, ici SHORT affiche en dd/mm/yyyy', 'indiquer si on veut aussi afficher l'heure')```

On parcours le resulat qu'on affiche à l'écran via ```echo``` pour parcourir toute les lignes on fait une boucle

```
    foreach( $datas as $data){
            echo'<tr>';
                echo'<td>'.$data['id'].'</td>';
                echo'<td>'.$data['nom'].' '.$data['prenom'].'</td>';
                echo'<td>'.$data['cp'].' '.$data['ville'].'</td>';
                echo'<td>'.$intlDateFormatter->format(strtotime($data['date_entree'])).'</td>';
                echo '<td><a href="edit.php?edit=1&id='.$data['id'].'">Modifier</a> <a href="delete.php?id='.$data['id'].'">Supprimer<a><td>';
            echo '</tr>';
        }
```

Ici on nomme les éléments du tableau ```$data``` car ils sont une partie de l'ensemble de données ```$datas```
Il faut aussi que le tableau transforme la chaine de caractères de la colone ```date_entree``` en date, on demande alors  à ```$intlDateFormatter``` de donner le format de date français à ```$data['date_entree']``` qui lit la date en format US sur le serveur grace à ```strtotime```

Pour modifier et supprimer les valeurs, on insère des liens hypertexte avec les balises html ```a```. Pour ce qui est de l'edition, ```edit=1&id``` précisera à l'```url``` les informations que réclamerons les conditions de la page ```edit.php```. Nous nous y attarderons plus bas.

Afin d'éviter de laisser un tableau vide, on va créer une condition ou afficher une phrase pour indquer qu'il fonctionne bien mais n'a rien à afficher

```
    if(count($datas) === 0){
                echo'<p> Ancun stagiaire </p>';
            }
```

Ici, on utilise ```if``` pour créer une condition, on lui demande alors de vérifier le tableau ```$datas``` via l'instruction ```count```. On vérifie si la réponse est nulle avec ```===``` qui doit correspondre en ```type``` et en ```valeur``` à ```0```, soit une valeur nulle et un type indentique (On met ```===``` afin de préciser la vérification, ```==``` ne cherche à correspondre qu'à la ```valeur```)

Le tableau est maintenant prêt à afficher son contenu, il nous reste maintenant à insérer, modifier ou supprimer des lignes dans la base de données. Pour effectuer ces tâches, on créé deux nouvelles pages: ```edit.php``` et ```delete.php```.

## Editer les valeurs dans le tableau

### Ajouter/Editer des valeurs

A l'aide de ```php``` et de ```sql``` nous allons interragir directement avec la base de donnée depuis le navigateur. Pour commencer nous allons créer un fichier ```edit.php``` qui contiendra les requêtes d'insertion et d'editions. Les deux ayant besoin de formulaires similaires et touchant aux mêmes données, il suffira d'ajouter des conditions afin de ne pas rajouter de code superflux.

Pour obtenir les autorisations vis à vis de la ```DataBase```, on commence par lier ```delete.php``` à ```db.php```

```
    <?php
        require_once('db.php');
    ?>
```

Ensuite, il faut créer l'espace de travail à partir d'une structure ```HTML```:

```
    <?php
        if( isset($_GET['id']) && isset($_GET['edit'])){
            echo'<h1>Modifier des informations</h1>';
        }else{
            echo'<h1>Ajouter un(e) stagiaire</h1>';
        }
    ?>
    <div>
        <form action="" method="post">
            <div>
                <input type="text" name="nom" id="nom" placeholder="Nom" value="<?=$nom; ?>">
            </div>
            <div>
                <input type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?=$prenom; ?>">
            </div>
            <div>
                <input type="text" name="adresse" id="adresse" placeholder="Adresse" value="<?=$adresse; ?>">
            </div>
            <div>
                <input type="text" name="complement" id="complement" placeholder="Complément Adresse" value="<?=$complement; ?>">
            </div>
            <div>
                <input type="text" name="cp" id="cp" placeholder="Code Postal" value="<?=$cp; ?>">
                <input type="text" name="ville" id="ville" placeholder="Ville" value="<?=$ville; ?>">
            </div>
            <div>
                <input type="date" name="date" id="date" placeholder="Date d'entrée" value="<?=$dateEntry; ?>">
            </div>

            <?php
                if( isset ($_GET['id']) && isset($_GET['edit'])){
                    $texteButton = "Modifier";
                }else{
                    $texteButton = "Ajouter";
                }
            ?>

            <div>
                <button type="submit"><?=$texteButton ?></button>
            </div>

            <?php 
                if( isset($_GET['id']) && isset($_GET['edit'])){
            ?>
                    <input type="hidden" name="edit" value="1" />
                    <input type="hidden" name="id" value="<?=$id ?>">
            <?php
                }
            ?>
        </form>
    </div>
```

On commence par différencier les informations affichées, on déclare donc une condition pour la requête ```Edition```. On utilise donc ```if``` pour imposer une condition, ici, on regarde dans l'url grace à l'instruction ```isset```, il va alors vérifier s'il peut prendre les valeurs de ```id``` et ```edit```. S'il peut récupérer ces valeurs, il affichera donc le résultat de la requête ```Edition```.
Si ces conditions ne sont pas respectées, il affichera donc le resultat de la requête ```Inserion```.

Dans une ```div``` on créé un formulaire via les balises ```<form></form>``` a qui on ajoute la ```method="post"``` qui demandera de remplir les formulaires. On sépare ensuite les différentes zones d'```input``` par des div, on leur donne un ```type```: ```text``` pour que les formulaires soient des zones de texte à remplir, ```date``` pour avoir le format d'écriture souhaité ou sélectionner une date va un calendrier et ```hidden``` pour n'aparaitre que sous certaines conditions. On les identifie aussi via leur ```id```, on pré-remplie aussi la zone de texte grâce au ```placeholder``` pour indiquer l'information demandée et on fini par leur attribuer une valeur avec ```value```, ici il s'agit de variables PHP.

```
    $nom = '';
        $prenom = '';
        $adresse = '';
        $complement = '';
        $cp = '';
        $ville = '';
        $dateEntry = '';
        $id = '';
        $error = false;
```

On créé donc des variables vides, on pourra ainsi avoir un formulaire vide au chargement de la page, elles doivent être au dessus de toutes requêtes.
Une variable ```$error``` est créée afin de pouvoir annuler des requêtes si certaines conditions ne sont pas correctement complétées.
Une fois les variables créées, on va vérifier via l'```URL``` si on est en mode ```Insertion``` ou ```Edition```

```
    if(isset($_GET['id']) && isset($_GET['edit'])){
        $sql = 'SELECT id, nom, prenom, adresse, complement_adresse, cp, ville, date_entree FROM stagiaire WHERE id=:id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if( gettype($data) === "boolean"){            
            header('Location: index.php');

            exit;
        }
    $nom = $data['nom'];
    $prenom = $data['prenom'];
    $adresse = $data['adresse'];
    $complement = $data['complement_adresse'];
    $cp = $data['cp'];
    $ville = $data['ville'];
    $dateEntry = $data['date_entree'];
    $id = htmlentities($_GET['id']);
    }
```


Afin de protéger l'id ciblée de toute ```insertion de code``` on utilise ```bindParam(':id', $_GET['id'], PDO::PARAM_INT)``` qui va lier la valeur de l'```id``` à sa variable, ainsi, même si quelqu'un tente de modifier cette valeur via l'url, la requête ne sera pas effectuée.
Pour finir, on vérifie via ```gettype``` si la valeur du ```$data``` existe, si ce n'est pas le cas, en cas de ```var_dump``` ce n'est pas un ```array``` qui va apparaitre, mais un ```boolean```: c'est marqueur de vérité, ici il annocera toujours un ```false``` qui indiquera que la valeur recherchée n'existe pas.
Si c'est un ```boolean``` alors la page sera automatiquement redirigée vers ```index.php```, via la requête ```header``` qui envoie le lien qui lui est attribué dans la barre de navigation. On termine ensuite le script grâce ```exit``` ou ```die``` pour eviter qu'il ne boucle dans l'index.

Si les conditions son respectées, les zone d'```input``` afficheront les ```valeurs à éditer``` extraites de la base de données par la requête ```$sql```. Pour protéger ```$id```qui ne doit pas être modifié, nous rajoutons ```htmlentities``` pour eviter l'injection de HTML dans la base de données.

Si ce n'est pas une ```Edition```, il s'agit donc automatiquement d'une ```Insertion```.
Pour insérer des valeurs dans la ```DataBase``` en toute sécurité, on va y imposer des conditions

```
if(count($_POST) > 0 ){

        if(strlen(trim($_POST['nom'])) !== 0){
            $nom = trim($_POST['nom']);
        }else{
            $error = true;
        }

        if(strlen(trim($_POST['prenom'])) !== 0){
            $prenom = trim($_POST['prenom']);
        }else{
            $error = true;
        }

        if(strlen(trim($_POST['adresse'])) !== 0){
            $adresse = trim($_POST['adresse']);
        }else{
            $error = true;
        }

        if(strlen(trim($_POST['cp'])) !== 0){
            $cp = trim($_POST['cp']);
        }else{
            $error = true;
        }

        if(strlen(trim($_POST['ville'])) !== 0){
            $ville = trim($_POST['ville']);
        }else{
            $error = true;
        }
        if(strlen(trim($_POST['date'])) !== 0){
            $dateEntry = trim($_POST['date']);
        }else{
            $error = true;
        }

        $complement = trim($_POST['complement']);

        if(isset($_POST['edit']) && isset($_POST['id'])){
            $id = htmlentities($_POST['id']);
        }

```
On commence par imposer une condition ```if``` qui va vérifier via ```count``` si tout les champs sont remplis. Chaque champ va vérifier à son tour via une autre condition ```if``` quel message il doit envoyer à ```count```, ```strlen``` va vérifier que la nombre de caractères soit différent de zéro, ici représenté par ```!==0```, le ```!``` représentant la négation de ```==``` qui demande une égalité de valeur. Quand à ```trim``` il est là pour corriger les erreurs de frappe en supprimant les espaces avant et après chaque chaine de caractères, évitant ainsi des interférences dans la recherche des chaines de caractères dans la base de données. 

Si ces conditions sont respectées, les valeurs seront intégrées dans la base de données, mais au moindre problème, la condition ```else``` va déclencher ```$error``` qui interrompera le formulaire. Seules exeptions ici, ```$complement``` qui peut rester vide puisqu'elle n'est pas obligatoire et ```$id``` qui ne peut être modifiée et est protégée par ```htmlentities```.

Il reste maintenant à préparer la requête pour insérer les valeurs dans la base de données.

```
if($error === false){

            if(isset($_POST['edit']) && isset($_POST['id'])){
                $sql = 'UPDATE stagiaire SET nom=:nom, prenom=:prenom, adresse=:adresse, complement_adresse=:complement, cp=:cp, ville=:ville, date_entree=:date WHERE id=:id';
            }else{
                $sql = "INSERT INTO stagiaire(nom,prenom,adresse,complement_adresse,cp,ville,date_entree) VALUES(:nom,:prenom,:adresse,:complement,:cp,:ville,:date)";
            }
            $sth = $dbh->prepare($sql);
            //on protège le database d'injection sql
            $sth->bindParam(':nom', $nom, PDO::PARAM_STR);
            $sth->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $sth->bindParam(':adresse', $adresse, PDO::PARAM_STR);
            $sth->bindParam(':complement', $complement, PDO::PARAM_STR);
            $sth->bindParam(':cp', $cp, PDO::PARAM_STR);
            $sth->bindParam(':ville', $ville, PDO::PARAM_STR);
            $sth->bindValue(':date', strftime("%Y-%m-%d",strtotime($dateEntry)), PDO::PARAM_STR);
            if(isset($_POST['edit']) && isset($_POST['id'])){
                $sth->bindParam('id', $id, PDO::PARAM_INT);
            }
            $sth->execute();

            header('Location: index.php');
```

Pour différencier l'```Edition``` de l'```Insertion```, on va créer une autre condition de vérification grâce à ```if``` et ```isset```, si les conditions sont respectées, la requête sql ```UPDATE``` mettra à jour les informations dans la base de données, sinon la requête ```INSERT``` les y intégrera.

Il reste à protéger les injections avec ```bindParam```. La valeur de ```date``` doit être protégée différemment: comme nous modifions son format avec ```strftime```, ```bindParam``` ne peut lier la variable qui est modifié lors de l'injection, nous lui demandrons donc de lier sa valeur via ```bindValue```. Quand à ```$id```, puisqu'il n'est pas injecté manuellement, il ne doit être protégé que lorsque les valeurs sont modifiés, sa sécurité n'est donc demandée qu'en cas d'```Edition```.

Une fois les données protégées, on execute ```$sth``` qui les injecte dans le serveur. Une fois la requête terminée, ```header``` nour redirige vers la ```Location``` qui lui est alouée (Mettre le "L" en majuscule et un espace après les ":"), ici ```index.php```.
L'```Editon```/```Insertion``` est donc terminée, il reste maintenant à intégrer la ```Supression```

### Supprimer une ligne du tableau

Pour créer une ```requête de supression```, on commence d'abord par l'isoler sur un fichier que l'on nommera ```delete.php```

```
    <?php
    require_once('db.php');

    if (isset( $_GET['id'])){
        $sql = 'DELETE FROM stagiaire WHERE id=:id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $sth->execute();

    }
    
    header('Location: index.php');

    ?>
```

Pour obtenir les autorisations vis à vis de la ```DataBase```, on commence par lier ```delete.php``` à ```db.php```. 
Pour la supression d'une ligne de tableau, il suffit de trouver son ```id```, pour ça, on commence par vérifier si dans l'url avec ``` isset``` si il est possible de prélever ```id``` avec ```$_GET```.
Si l'```id``` a été trouvée, il suffit d'executer une requête ```$sql``` pour lui demander de supprimer la ligne ou se trouve la valeur de ```id```.
Une fois la ligne trouvée et la valeur d'```id``` protégée par ```bindParam```, la requête s'execute.
Dès que la requête est terminée, ```header``` nous renvoie directement sur sa ```location``` soit ```index.php```.
La ligne du tableau a été supprimée