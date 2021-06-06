<?php
session_start();
$bdd = new PDO('mysql:host=127.0.0.1;dbname=myteam2', "root");
if (isset($_SESSION['id'])) {
    $requser = $bdd->prepare("SELECT * FROM ");
    $requser->execute(array($_SESSION['id']));
    $user = $requser->fetch();
    if (isset($_POST['newpseudo']) and !empty($_POST['newpseudo'])) {
        $newpseudo = htmlspecialchars($_POST['newpseudo']);
        $insertpseudo = $bdd->prepare("UPDATE");
        header('Location: Profil.php?id=' . $_SESSION);
    }
    if (isset($_POST['newmail']) and !empty($_POST['newmail'])) {
        $newmail = htmlspecialchars($_POST['newmail']);
        $insertpseudo = $bdd->prepare("UPDATE");
        $insertpseudo->execute(array($newpseudo));
        header('Location: profil.php?id=' . $_SESSION);
    }
    if (isset($_POST['newmail']) and !empty($_POST)) {
        $newmail = htmlspecialchars($_POST['newmail']);
        $insertmail = htmlspecialchars($_POST['newmail']);
        // $insertmail->execute(array($newmail));
        header('Location: Profil.php?id=' . $_SESSION);
    }
    if (isset($_POST['newmail']) and !empty($_POST)) {
        $newmail = htmlspecialchars($_POST['newmail']);
        $insertmail = $bdd->prepare("UPDATE");
        header('Location: Profil.php?id=' . $_SESSION);
    } else {
        $msg = "Vos deux mdp ne correspondent pas";
    }
}
if (isset($_FILES['avatar']) and !empty($_FILES['avatar']['name'])) {
    $taillemax = 2097152;
    $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
    if ($_FILES['avatar']['size'] <= $taillemax); {
        $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
        if (in_array($extensionUpload, $extensionsValides)) {
            $chemin = "membres/avatars/" . $_SESSION['id'] . "." . $extensionUpload;
            $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
            if ($resultat) {
                $updateavatar = $bdd->prepare('UPDATE membres SET avatar = :avatar WHERE id = :id');
                $updateavatar->execute(array(
                    'avatar' => $_SESSION['id'] . "." . $extensionUpload,
                    'id' => $_SESSION['id']
                ));
                header('Location: Profil.php?id=' . $_SESSION);
            } else {
                $msg = "Erreur durant l'importation du fichier";
            }
        } else {
            $msg = "Votre photo de profil doit être en format jpg, jpeg, gif, png";
        }
    }
}
if (isset($_POST['newpseudo']) and $_POST['newpseudo'] == $user); {
    header('Location: profil.php.id=' . $_SESSION['id']);
}



?>



<!DOCTYPE html>
<html>
<?php
$title = 'Mon profil';
include('includes/head.php');
?>

<body>



    <?php include('includes/header.php'); ?>



    <main>
        <div class="container">
            <?php include('includes/message.php'); ?>



            <?php
            if (isset($_SESSION['email'])) {

                include('includes/db.php');



                // récupérer les infos de l'utilisateur
                $q = 'SELECT email, image FROM users WHERE email = :email';
                $q = 'SELECT Last_Name, image FROM users WHERE Last_Name = :Last_Name';
                $q = 'SELECT First_Name, image FROM users WHERE First_Name = :First_Name';



                $req = $bdd->prepare($q);
                $req->execute([
                    'email' => $_SESSION['email']
                ]);
                $req = $bdd->prepare($q);
                $req->execute([
                    'Last_Name' => $_SESSION['Last_Name']
                ]);
                $req = $bdd->prepare($q);
                $req->execute([
                    'First_Name' => $_SESSION['First_Name']
                ]);



                $user = $req->fetch(PDO::FETCH_ASSOC); // Récupération du premier résultat



                var_dump($user);



                // Affichage de l'email
                echo '<p>Email : ' . $user['email'] . '</p>';



                // Affichage de l'image de profil
                echo '<img src="uploads/' . $user['image'] . '" alt="Image de profil">';
                echo '<p>First_Name : ' . $user['First_Name'] . '</p>';
                echo '<p>Last_Name : ' . $user['Last_Name'] . '</p>';
            } else {
                echo '<p>Utilisateur introuvable !</p>';
            }
            ?>
        </div>
    </main>



    <?php include('includes/footer.php'); ?>



</body>

</html>