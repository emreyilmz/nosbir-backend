<?php
require_once("../sistem/ayar.php");
require_once("../yardımcılar/token-dogrula.php");
if (isset($data)) {

    $token = json_decode(TokenDogrula::dogrula()); // token doğrula fonksiyonundan dönen jsonı decode ediyoruz
    if ($token->{"durum"} == 0) { // fonsiyondan durum 0 dönmüşse token doğrulanamadı hatası verip işlemi sonlandırıyoruz
        echo json_encode(array(
            "durum" => 0,
            "mesaj" => "Token doğrulanamadı."
        ));
        die(); // işlem sonlandırma bundan sonrası çalışmayacak
    }

    $id=$token->{"token"}->{"data"}->{"id"}; // eğer token normal geldiyse token içinden üye id yi alıyoruz
    $postId=$data["postId"];
    $tur=$data["tur"];

    $begenme=$db->prepare("select * from likes l
                        where l.user_id=:user_id and l.post_id=:post_id limit 1");
    $begenme->bindParam(":user_id",$id);
    $begenme->bindParam(":post_id",$postId);
    $begenme->execute();
    if($begenme->rowCount()){
        $begeni=$begenme->fetch(PDO::FETCH_ASSOC);
        if($begeni["type"]!=$tur){
            $guncelle=$db->prepare("update likes set type=:tur where user_id=:user_id and post_id=:post_id");
            $guncelle->bindParam(":user_id",$id);
            $guncelle->bindParam(":post_id",$postId);
            $guncelle->bindParam(":tur",$tur);

            if($guncelle->execute()){
                echo json_encode(array(
                    "mesaj" => "Begeni başarıyla kaydedildi",
                    "durum" => 1
                ));
            }else{
                echo json_encode(array(
                    "mesaj" => "Kaydedilirken bir hata oluştu",
                    "durum" => 0
                ));
            }
        }else{
            $sil->$db->prepare("delete likes set type:tur where user_id=:user_id and post_id=:post_id");
            $sil->bindParam(":user_id",$id);
            $sil->bindParam(":post_id",$postId);
            $sil->bindParam(":tur",$tur);

            if($sil->execute()){
                echo json_encode(array(
                    "mesaj" => "Begeni başarıyla silindi",
                    "durum " => 0
                ));   
            }else{
                echo json_encode(array(
                    "mesaj" => "Begeni silinirken bir sorun oluştu",
                    "durum" => 0
                ));
            }
            
        }
    }else{
        $kaydet=$db->prepare("INSERT INTO likes SET
        user_id=:user_id,
        post_id=:post_id,
        type=:type");

        $kaydet->bindParam(":user_id",$id);
        $kaydet->bindParam(":post_id",$postId);
        $kaydet->bindParam(":type",$tur);
        
        if($kaydet->execute()){
            echo json_encode(array(
                "mesaj" => "Begeni başarıyla kaydedildi",
                "durum" => 1
            ));
        }else{
            echo json_encode(array(
                "mesaj" => "Kaydedilirken bir hata oluştu",
                "durum" => 0
            ));
            
        }
    }
}
        
    


 





?>