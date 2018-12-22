

<?php 

session_start();
if(!array_key_exists('tracking', $_SESSION)){
    $_SESSION['tracking']="";
}

if($_SESSION['tracking']==''){
  
  /*  if(empty($_SESSION['ListeFavoris'])){
        $_SESSION['ListeFavoris']="";
    }if(empty( $_SESSION['MembrePropriete'])){
        $_SESSION['MembrePropriete']="hidden";
    }if(empty( $_SESSION['EtatConnectionMembre'] )){
        $_SESSION['EtatConnectionMembre'] ="Membre Connecter";
    }if(empty($_SESSION['ActionMembre'])){
        $_SESSION['ActionMembre']="Membre.php";
    }if(empty($_SESSION['Membre'])){
        $_SESSION['Membre']="Membre";
    }*/
    $_SESSION['ListeFavoris']="";
    $_SESSION['MembrePropriete']="hidden";
    $_SESSION['EtatConnectionMembre'] ="Membre Connecter";
    $_SESSION['ActionMembre']="Membre.php";
    $_SESSION['tracking']="";
    $_SESSION['Membre']="Membre";
    $_SESSION['Username']="";

}
?>
<?php
date_default_timezone_set("America/Toronto");
$CheminImagePromo = "";
$Desc_pro = "";
$MusicJukeBox = "";
$actionRecherche = "";
$nomRecherche="";
$valeurRecherche="";
$LectureEnCours = "";
$FavorisExiste = "";





$connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');

// On retourne une image aléatoire pour une promotion
$stid = oci_parse($connect, "select * from(select * from TP2_PROMOTION order by dbms_random.value) where rownum=1");
oci_execute($stid);

if (($result = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    
    echo "<h3>{$result["DESC_PRO"]}</h3><br>";
    
    $CheminImagePromo = $result["CHEMIN_IMAGE_PRO"] . "/" . $result["NO_PROMOTION"] . ".jpg";
    
    $Desc_pro = $result["DESC_PRO"];
}

$stid = oci_parse($connect, "select TI.CODE_TITRE, AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT from TP2_TITRE TI
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE");
oci_execute($stid);
$MusicJukeBox = "<select name=\"MusicJukeBox\">";
while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    
    $MusicJukeBox .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
}
$MusicJukeBox .= "</select>";
oci_close($connect);
$connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
$stid = oci_parse($connect, "begin TP3_VERFIER_LECTURE; end;");
oci_execute($stid);
oci_close($connect);
$connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');

$stid = oci_parse($connect, "select LE.CODE_TITRE, AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT from TP3_LECTURE LE
                                        inner join TP2_TITRE TI on LE.CODE_TITRE = TI.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        order by LE.ID_LECTURE asc");
oci_execute($stid);
$LectureEnCours = "";
while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    
    $LectureEnCours .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
}

$actionRecherche = "RechercheChanson.php";
$nomRecherche="Rechercher";
$valeurRecherche="Rechercher";

oci_close($connect);
?>
<?php

if (isset($_REQUEST["Ok"])) {
    
    $resultsong = "";
    
   
    $TitreValeur = "''";
    $AlbumValeur = "''";
    $ArtisteValeur = "''";
    if ($_POST["NomTitre"] != "") {
        $TitreValeur = "'%{$_POST['NomTitre']}%'";
    }
    if ($_POST['Album'] != "") {
        $AlbumValeur = "'%{$_POST['Album']}%'";
    }
    if ($_POST['Artiste'] != "") {
        $ArtisteValeur = "'%{$_POST['Artiste']}%'";
    }
    
    $connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
    $stid = oci_parse($connect, "select TI.CODE_TITRE, AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT from TP2_TITRE TI
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        where TI.TITRE_TIT like {$TitreValeur} or AL.TITRE_ALB like {$AlbumValeur}
                                        or AR.NOM_ART like {$ArtisteValeur}");
    
    oci_execute($stid);
    
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        
        $resultsong .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
    }
    oci_close($connect);
    $MusicJukeBox = "<select name=\"MusicJukeBox\">";
    if (empty(($resultsong))) {
        $MusicJukeBox = "<option value=\"aucun\"></option>";
    } else {
        $MusicJukeBox .= $resultsong;
    }
    $MusicJukeBox .= "</select>";
    
    $actionRecherche = "";
    $nomRecherche="Tous";
    $valeurRecherche="Tous";
}

?>
<?php

if (isset($_REQUEST['Tous'])) {
    $connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
    $stid = oci_parse($connect, "select TI.CODE_TITRE, AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT from TP2_TITRE TI
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE");
    oci_execute($stid);
    $MusicJukeBox = "<select name=\"MusicJukeBox\">";
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        
        $MusicJukeBox .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
    }
    $MusicJukeBox .= "</select>";
    oci_close($connect);
    
    $actionRecherche = "RechercheChanson.php";
    $nomRecherche="Rechercher";
    $valeurRecherche="Rechercher";
}
?>


<?php
if (isset($_REQUEST['AjouterListeLecture'])) {
    
    $pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache');

    $connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
    if ($pageRefreshed != 1) {
        $LectureEnCours="";
        $datecommande = date("d-m-Y H:i:s");
        
        $stid = oci_parse($connect, "insert into TP3_LECTURE(CODE_TITRE,DATE_HEURE_DEBUT_LEC) values('{$_POST['MusicJukeBox']}',to_timestamp('{$datecommande}','DD-MM-RRRR HH24:MI:SS'))");
        oci_execute($stid);
        
        $stid = oci_parse($connect, "insert into TP2_COMMANDE(NOM_USAGER_CLIENT,CODE_TITRE,DATE_HEURE_COM,MNT_COUT_COM,EST_PAYEE_COM)
                   values('{$_SESSION['Username']}','{$_POST['MusicJukeBox']}',to_timestamp('{$datecommande}','DD-MM-RRRR HH24:MI:SS'),
                    0.50,0)");
        oci_execute($stid);
    }
    $stid = oci_parse($connect, "select LE.CODE_TITRE, AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT from TP3_LECTURE LE
                                        inner join TP2_TITRE TI on TI.CODE_TITRE = LE.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        order by LE.ID_LECTURE asc");
    oci_execute($stid);
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        
        $LectureEnCours .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
    }
    $stid = oci_parse($connect, "select FA.CODE_TITRE,AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT
                                        from TP2_FAVORIS FA
                                        inner join TP2_TITRE TI on FA.CODE_TITRE = TI.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        where FA.NOM_USAGER_CLIENT='{$_SESSION['Username']}'");
    oci_execute($stid);
    $_SESSION['ListeFavoris'] = "<h5>Liste Favoris</h5><select name=\"ChansonFavoris\">";
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        
        $_SESSION['ListeFavoris'] .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
    }
    $_SESSION['ListeFavoris'] .= "</select><br>";
    
    oci_close($connect);
}

?>
<?php
if (isset($_REQUEST['AjoutFavoris'])) {
   
    try {
        $FavorisExiste="";
        $connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
        $stid = oci_parse($connect, "select count(*) from TP2_FAVORIS where NOM_USAGER_CLIENT='{$_SESSION['Username']}' and CODE_TITRE='{$_POST['LectureEnCours']}'");
        oci_execute($stid);
        if(($row = oci_fetch_array($stid, OCI_NUM)) != false){
            if($row[0]==0){
                $stid = oci_parse($connect, "insert into TP2_FAVORIS(NOM_USAGER_CLIENT,CODE_TITRE,NB_FOIS_JOUE_FAV)
                    values('{$_SESSION['Username']}','{$_POST['LectureEnCours']}',40)");
                oci_execute($stid);
                $stid = oci_parse($connect, "select FA.CODE_TITRE,AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT
                                        from TP2_FAVORIS FA
                                        inner join TP2_TITRE TI on FA.CODE_TITRE = TI.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        where FA.NOM_USAGER_CLIENT='{$_SESSION['Username']}'");
                oci_execute($stid);
                $_SESSION['ListeFavoris'] = "<h5>Liste Favoris</h5><select name=\"ChansonFavoris\">";
                while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                    
                    $_SESSION['ListeFavoris'] .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
                }
                
                
                $_SESSION['ListeFavoris'] .= "</select><br>";
            }else{
                $FavorisExiste = "<h5>Cette chanson existe déjà dans votre liste favoris.</h5>";
            }
        }
      
   
        oci_close($connect);
    } catch (Exception $e) {
        $FavorisExiste = $e->getMessage();
    }
  
}
if (isset($_REQUEST['SupFavoris'])) {
    $FavorisExiste="";
    $connect = oci_connect('C##JACHA199', 'bd111180596', 'ift-p-ora12c.fsg.ulaval.ca:1521/ora12c');
    if(!empty($_POST['ChansonFavoris'])){
        $stid = oci_parse($connect, "delete from TP2_FAVORIS where NOM_USAGER_CLIENT='{$_SESSION['Username']}' and CODE_TITRE='{$_POST['ChansonFavoris']}'");
        oci_execute($stid);
        $stid = oci_parse($connect, "select FA.CODE_TITRE,AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT
                                        from TP2_FAVORIS FA
                                        inner join TP2_TITRE TI on FA.CODE_TITRE = TI.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        where FA.NOM_USAGER_CLIENT='{$_SESSION['Username']}'");
        oci_execute($stid);
        $_SESSION['ListeFavoris'] = "<h5>Liste Favoris</h5><select name=\"ChansonFavoris\">";
        while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
            
            $_SESSION['ListeFavoris'] .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
        }
        $_SESSION['ListeFavoris'] .= "</select><br>";
    }else{
        $FavorisExiste = "<h5>Vous n'avez aucune chanson dans votre liste favoris.</h5>";
    }
    
  

    oci_close($connect);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />

<title>Bienvenue/Welcome au Rock'n' Roll Food</title>

</head>
<body style="text-align: center;">

	<img alt="<?php echo $Desc_pro;?>"
		src="<?php echo $CheminImagePromo;?>">
	<br>
	<h5>Les Chansons du JukeBox</h5>


	<form method="post" action="PagePrinpale.php">
		<?php echo $MusicJukeBox;?>
		<input type="<?php echo $_SESSION['MembrePropriete'];?>"
			name="AjouterListeLecture" value="Commender"><br>

		<h5>Liste de lecture en cours</h5>
		<select name="LectureEnCours">
		<?php echo $LectureEnCours;?>
	</select>
	

		<?php echo $_SESSION['ListeFavoris'];?>
		<input type="<?php echo $_SESSION['MembrePropriete'];?>" name="AjoutFavoris"
			value="+ Favoris"> 
		<input type="<?php echo  $_SESSION['MembrePropriete'];?>"
			name="SupFavoris" value="- Favoris">
		

	</form>
	<?php echo $FavorisExiste;?>

	
	<form method="post" action="<?php echo $actionRecherche;?>" >
        <input type="submit" name="<?php echo $nomRecherche;?>"value="<?php echo $valeurRecherche;?>"/>
     </form>
     
	<form method="post" action="<?php echo $_SESSION['ActionMembre'] ;?>">
		<input type="submit" name="<?php echo $_SESSION['Membre'];?>"
			value="<?php echo $_SESSION['EtatConnectionMembre'];?>"><br>

	</form>



</body>
</html>
