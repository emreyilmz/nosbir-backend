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

    $postId=$_GET["postId"];

    $cek=$db->prepare("select user_id from posts where post_id=:post_id limit 1");
    $cek->bindParam(":post_id",$postId);
    if($cek->execute()){
        $row=$cek->fetch(PDO::FETCH_ASSOC);

    
        if($row["uyeId"]==$id){
            $sil=$db->prepare("update posts set post_statu=0 where post_id= :post_id and user_id= :user_id");
            $sil->bindParam("post_id",$postId);
            $sil->bindParam("user_id",$id);
            if($sil->execute()){
                echo json_encode(array(
                    "mesaj" => "Post başarıyla silindi",
                    "durum" => 1
                ));
    
            }else{
                echo json_encode(array(
                    "mesaj" => "Post silinirken bir sorun oluştu",
                    "durum" => 0
                ));
    
            }
        }else{
            echo json_encode(array(
                "mesaj" => "Yetkiniz bulunmamaktadır",
                "durum" => 0
            ));
        }

    }else{
        echo json_encode(array(
            "mesaj" => "Teknik bir sorun oluştu",
            "durum" => 0
        ));
    }

}
?>