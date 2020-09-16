<?php
require_once("../sistem/ayar.php");
require_once("../yardımcılar/token-dogrula.php");
if (isset($data)) {

    $token = json_decode(TokenDogrula::dogrula()); 
    if ($token->{"durum"} == 0) { 
        echo json_encode(array(
            "durum" => 0,
            "mesaj" => "Token doğrulanamadı."
        ));
        die(); 
    }

    $id=$token->{"token"}->{"data"}->{"id"}; 

    $icerik=strip_tags(trim($data["icerik"]));
    $yorumId=strip_tags(trim($data["id"]));

    $cek=$db->prepare("select uyeId from answer where answer_id = :answer_id and user_id = :user_id");
    $cek->bindParam(":answer_id",$yorumId);
    $cek->bindParam(":user_id",$id);

    if($cek->execute()){

        if($cek->rowCount()){

            if(!$icerik){
                echo json_encode(array(
                    "mesaj" => "Lutfen boş kısım bırakmayın",
                    "durum" => 0
                ));
            }else{
                $guncelle=$db->prepare("update answer set 
                                        text=:text where 
                                        answer_id=:answer_id and
                                        user_id=:user_id");
                $guncelle->bindParam(":text",$icerik);
                $guncelle->bindParam(":answer_id",$yorumId);
                $guncelle->bindParam(":user_id",$id);

                if($guncelle->execute()){
                    echo json_encode(array(
                        "mesaj" => "Yorum başarıyla güncellendi",
                        "durum" => 1
                    ));
                }else{
                    echo json_encode(array(
                        "mesaj" => "Yorum güncellenirken bir sorun oluştu",
                        "durum" => 0
                    ));
                }

            }


        }else{
            echo json_encode(array(
                "mesaj"=>"Yetkiniz Bulunmamaktadır.",
                "durum"=>0
            ));
        }
    }else{
        echo json_encode(array(
            "mesaj"=>"Teknik hata oluştu.",
            "durum"=>0
        ));
    }




}
?>