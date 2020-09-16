<?php
require_once("../sistem/ayar.php");
require_once("../yardımcılar/token-dogrula.php");
if (isset($_GET)) {

    $token = json_decode(TokenDogrula::dogrula()); 
    if ($token->{"durum"} == 0) { 
        echo json_encode(array(
            "durum" => 0,
            "mesaj" => "Token doğrulanamadı."
        ));
        die(); 
    }

    $id=$token->{"token"}->{"data"}->{"id"}; 
    $yorumId=$_GET["id"];

    $cek=$db->prepare("select * from answer where answer_id=:answer_id and user_id = :user_id limit 1");
    $cek->bindParam(":answer_id",$yorumId);
    $cek->bindParam(":user_id",$id);
    if($cek->execute()){

        if($cek->rowCount()){

            $sil=$db->prepare("delete from answer where answer_id = :answer_id and user_id = :user_id");
            $sil->bindParam(":answer_id",$yorumId);
            $sil->bindParam(":user_id",$id);

            if($sil->execute()){
                echo json_encode(array(
                    "mesaj" => "Yorum başarıyla silindi.",
                    "durum" => 1
                ));

            }else{
                echo json_encode(array(
                    "mesaj" => "Yorum silinirken bir hata oluştu",
                    "durum" => 0
                ));
            }
        }else{
            echo json_encode(array(
                "mesaj" => "Yetkiniz bulunmamaktadır.",
                "durum" => 0
            ));
        }
    }else{
        echo json_encode(array(
            "mesaj" => "Teknik bir hata oluştu.",
            "durum" => 0
        ));
    }






}
?>