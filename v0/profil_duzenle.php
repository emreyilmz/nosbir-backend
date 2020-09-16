<?php
require_once("../sistem/ayar.php");
require_once("../yardımcılar/token-dogrula.php");
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
if ($_POST) {

    $token = json_decode(TokenDogrula::dogrula()); 
    if ($token->{"durum"} == 0) { 
        echo json_encode(array(
            "durum" => 0,
            "mesaj" => "Token doğrulanamadı."
        ));
        die(); 
    }
    $id=$token->{"token"}->{"data"}->{"id"}; 
    $kadi=strip_tags(trim($_POST["kadi"]));

    $sec=$db->prepare("select * from users where user_id= :user_id limit 1");
    $sec->bindParam(":user_id",$id);
    $sec->execute(); 
    $row=$sec->fetch(PDO::FETCH_ASSOC);

    if($kadi){
        if(is_numeric($kadi)){
            echo json_encode(array(
                "mesaj" => "Kullanıcı adı sadece sayılardan oluşamaz",
                "durum" => 0
            ));
        }
        if(strlen($kadi)>=15){
            echo json_encode(array(
                "mesaj" => "Lutfen kullanıcı adınızı 15 karakterden büyük yapmayın.",
                "durum" => 0
                
            ));
        }
    }else{
        $kadi=$row["kadi"];
    }
    if(isset($_FILES["resim"]["name"])){
        $boyut=1024*1024*3;
        $uzanti=explode(".",$_FILES["resim"]["name"]);
        $uzanti=$uzanti[count($uzanti)-1]; 
        $adi=$kadi."-".date('m-d-Y')."-".rand(0,9999999).".".$uzanti;
        $yol="../resimler/".$adi;

        if($_FILES["resim"]["size"]>$boyut){
            echo json_encode(array(
                "mesaj" => "Dosya boyutu 3 mb'dan fazla olamaz",
                "durum" => 0
            ));     
         }else{
            $tip = ["image/jpeg","image/png","image/jpg","image/gif"];


            if(in_array($_FILES["resim"]["type"],$tip)){

                if(is_uploaded_file($_FILES["resim"]["tmp_name"])){
                    if(!move_uploaded_file($_FILES["resim"]["tmp_name"],$yol)){
                                        
                        echo json_encode(array(
                            "mesaj" => "Dosya taşınırken bir sorun oluştu.",
                            "durum" => 0
                        ));
                    }

                }else{
                    echo json_encode(array(
                        "mesaj" => "Dosya yuklenirken bir sorun oluştu.",
                        "durum" => 0
                    ));
                }

            }else{
                echo json_encode(array(
                    "mesaj" => "Resim dosya formatı geçersiz.",
                    "durum" => 0
                ));
            }
         }
        
    }else{
        $adi=$row["picture"];
    }

    $guncelle=$db->prepare("update users set
                        nick=:nick,
                        picture=:picture
                        where user_id = :user_id");

    $guncelle->bindParam(":user_id",$id);
    $guncelle->bindParam(":nick",$kadi);
    $guncelle->bindParam(":picture",$adi);


    if($guncelle->execute()){

        $secret_key="nosbirciler";
        $issuer_claim = "nosbir.com"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 12000; // token süresi
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            // "jti" => $tokenId,
            "data" => array(
                "id" => $row["user_id"],
                "kadi"=>$kadi,
                "resim"=>$adi,
                "mail" => $row["email"]
            )
        );
        $jwt=JWT::encode($token,$secret_key);

        echo json_encode(array(
            "token"=>$jwt,
            "mesaj" => "Profil başarıyla guncellendi.",
            "durum" => 1
        ));
    }else{
        echo json_encode(array(
            "mesaj" => "Profil guncellenirken bir sorun oluştu.",
            "durum" => 0
        ));
    }
}

?>