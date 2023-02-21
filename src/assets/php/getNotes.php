<?php
session_name('__Secure-PHPSESSID');
session_start();
if(!isset($_SESSION["nom"])) {
    header('HTTP/2.0 403 Forbidden');
} else {
    require_once "config.php";
    require_once "functions.php";
    $query = $PDO->prepare("SELECT * FROM `TABLE_NAME` WHERE user=:CurrentUser ORDER BY id DESC");
    $query->execute([':CurrentUser' => htmlspecialchars($_SESSION["nom"], ENT_QUOTES)]);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $title = $row['titre'];
        $title = strtr($title, array("&quot;" => "&lsquo;&lsquo;", "&lt;" => "&#x2190;", "&gt;" => "&#x2192;"));
        $couleur = $row['couleur'];
        $couleur = strtr($couleur, array("&quot;" => "&lsquo;&lsquo;", "&lt;" => "&#x2190;", "&gt;" => "&#x2192;"));
        $desc = decrypt_data($row['content'], $key);
        $desc = strtr($desc, array("\r\n" => "<br />", "\r" => "<br />", "\n" => "<br />", "&quot;" => "&lsquo;&lsquo;", "&lt;" => "&#x2190;", "&gt;" => "&#x2192;"));
        echo "<div class=\"note ".$couleur."\">
                <div class=\"details\">
                    <p>".$title."</p>
                    <span>".nl2br($desc)."</span>
                </div>
                <div class=\"bottom-content\">
                    <span>".$row['dateNote']."</span>
                    <div class=\"settings\">
                        <i title=\"Modifier\" onclick='updateNoteConnect(".$row['id'].",\"".$title."\",\"".$desc."\",\"".$couleur."\")'><i class=\"fa-solid fa-pen-to-square\" tabindex=\"0\"></i></i>
                        <i title=\"Supprimer\" onclick=\"deleteNoteConnect(".$row['id'].")\"><i class=\"fa-solid fa-trash\" tabindex=\"0\"></i></i>
                    </div>
                </div>
                <div>
                    <span class=\"status\"><i class=\"fa-solid fa-cloud\"></i>
                    Note chiffrée et stockée sur le cloud</span>
                </div>
            </div>";
    }
    $query->closeCursor();
    $PDO = null;
}