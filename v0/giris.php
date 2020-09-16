<?php
require_once("../sistem/ayar.php");
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
if(isset($data)){
    $kadi=@strip_tags(trim($data["kadi"]));
    $sifre = @md5(strip_tags(trim($data["sifre"])));
    if(!$kadi || !$sifre){
        echo json_encode(array(
            "mesaj" => "Kullanıcı Adı veya sifre bos bırakılamaz",
            "durum" => 0
        ));
    }else{
        $giris = $db->prepare("SELECT * FROM users WHERE nick= :nick AND passwd= :passwd LIMIT 1");
        $giris->bindParam(":nick",$kadi);
        $giris->bindParam(":passwd",$sifre);
        $giris->execute();
        $row = $giris->fetch(PDO::FETCH_ASSOC);

        if ($giris->rowCount()>0) {

            if ($row["user_status"] == 2) {
                echo json_encode(array(
                    "mesaj" => "Topluluğa aykırı davranışlarınızdan dolayı engellendiniz",
                    "durum" => 0
                ));
            }else{

                $kadi = $row["nick"];
                $eposta = $row["email"];
                $resim = $row["picture"];
                $id = $row["user_id"];
                
                // $tokenId=base64_encode(mcrypt_create_iv(32));
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
                        "id" => $id,
                        "kadi"=>$kadi,
                        "resim"=>$resim,
                        "mail" => $eposta
                    )
                );
                $jwt=JWT::encode($token,$secret_key);

                echo json_encode(array(
                    "durum" =>1,
                    "token" =>$jwt
                    // "suresi"=>$expire_claim 
                ));

            }

        }else{

            echo json_encode(array(
                "mesaj" => "Kullanıcı Bilgileri Bulunamadı",
                "durum" => 0
            ));
            
        }
    

    }
}



?>