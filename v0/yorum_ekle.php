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

    
    $icerik=strip_tags(trim($data["icerik"]));
    $postId=strip_tags(trim($data["postId"]));

    if(!$icerik){
        echo json_encode(array(
            "mesaj" => "İcerik kısmı boş bırakılamaz",
            "durum" => 0
        ));
    }else{
        //Efdal! bu kodla adam yorum attıktan 30 saniye sonra yorum atabilecek
        //kalsın mı? boylece bir fonksiyon yazıp saniye başı mesaj atamıyacak
        $yorumAyar=$db->prepare("select * from answers a where
                                    a.created_at>now() - interval 30 second 
                                    and user_id=: user_id");
                                    
        $yorumAyar->bindParam(":user_id",$id);
        $yorumAyar->execute();

        if($row=$yorumAyar->fetch(PDO::FETCH_ASSOC)){
            echo json_encode(array(
                "mesaj" => "30 saniye içinde birden fazla yorum yazamassınız.",
                "durum" => 0
            ));
        }else{
            $yorumEkle=$db->prepare("insert into answers set 
            user_id=:user_id,
            text=:text,
            post_id=:post_id");
            
            $yorumEkle->bindParam(":user_id",$id);
            $yorumEkle->bindParam(":text",$icerik);
            $yorumEkle->bindParam(":post_id",$postId);

            if($yorumEkle->execute()){
                echo json_encode(array(
                    "mesaj" => "Yorum başarıyla kaydedildi",
                    "durum" => 1
                ));
            }else{
                echo json_encode(array(
                    "mesaj" => "Yorum kaydedilirken bir hata oluştu.",
                    "durum" => 0
                ));
            }

            

        }
        
    }





}
?>

