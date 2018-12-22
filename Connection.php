<?php 
session_start();

?>
<?php


if (isset($_REQUEST['Connecter'])) {
   
    $connect = oci_connect("Insert les params for connetion");
    try {
        $stid = oci_parse($connect, "select count(*) nb from TP2_CLIENT where NOM_USAGER_CLIENT = '{$_POST['UserName']}' and MOT_DE_PASSE_CLI = '{$_POST['Password']}'");
        oci_execute($stid);
        if (($result = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS+OCI_NUM)) != false) {
            if ($result[0] != 0) {
                $_SESSION['Username']=$_POST['UserName'];
                $stid = oci_parse($connect, "select FA.CODE_TITRE,AR.NOM_ART,AL.TITRE_ALB,TI.TITRE_TIT
                                        from TP2_FAVORIS FA
                                        inner join TP2_TITRE TI on FA.CODE_TITRE = TI.CODE_TITRE
                                        inner join TP2_ALBUM AL on TI.NO_ALBUM = AL.NO_ALBUM
                                        inner join TP2_ARTISTE AR on TI.NO_ARTISTE=AR.NO_ARTISTE
                                        where FA.NOM_USAGER_CLIENT='{$_POST['UserName']}'");
                oci_execute($stid);
                $_SESSION['ListeFavoris'] = "<h5>Liste Favoris</h5><select name=\"ChansonFavoris\">";
                while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                    
                    $_SESSION['ListeFavoris'] .= "<option value=\"{$row["CODE_TITRE"]}\">{$row["CODE_TITRE"]} {$row["NOM_ART"]} {$row["TITRE_ALB"]} {$row["TITRE_TIT"]}</option>";
                }
                $_SESSION['ListeFavoris'] .= "</select><br>";
                $_SESSION['MembrePropriete'] = "submit";
                $_SESSION['EtatConnectionMembre'] = "Deconnecter";
                $_SESSION['Membre'] = "Deconnecter";
                $_SESSION['ActionMembre'] = "Connection.php";
                $_SESSION['tracking']="unMembreestconnecter";
                oci_close($connect);
            }else{
                $_SESSION['ListeFavoris'] = "<h4>Impossible de connecter!</h4><p>Veuillez reappuyer sur le boutton Membre Connecter pour recommencer.</p>";
                $_SESSION['tracking']="unMembreestconnecter";
                oci_close($connect);
            }
        }
    } catch (Exception $e) {
        $_SESSION['ListeFavoris']=$e->getMessage();
    }
    
  
}
if (isset($_REQUEST['Deconnecter'])) {
    $_SESSION['MembrePropriete'] = "hidden";
    $_SESSION['Membre'] = "Membre";
    $_SESSION['EtatConnectionMembre'] = "Membre Connecter";
    
    $_SESSION['ActionMembre']  = "Membre.php";
    $_SESSION['tracking']='';
}
header("Location: PagePrinpale.php");
exit;
?>