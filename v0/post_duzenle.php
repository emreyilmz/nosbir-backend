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

    $baslik = strip_tags(trim($data["baslik"]));
    echo $icerik = strip_tags(trim($data["icerik"]));
    $postId = strip_tags(trim($data["id"]));

    // TODO gönderilen postun giriş yapan kullanıcıya ait olup olmadığını kontrol et
    // ona ait değilse yetki hatası dönder ona aitse güncelle

    $cek=$db->prepare("select user_id from posts where post_id = :post_id limit 1");
    $cek->bindParam(":post_id",$postId);
    if($cek->execute()){
        $row=$cek->fetch(PDO::FETCH_ASSOC);
        if($row["uyeId"] != $id){
            echo json_encode(array(
                "mesaj"=>"Yetkiniz Bulunamamaktadır",
                "durum"=>0
            ));
            die();
        }
    }else{
        echo json_encode(array(
            "mesaj"=>"Teknik problem oluştu daha sonra tekrar deneyin",
            "durum"=>0
        ));
        die();
    }

    if(!$baslik || !$icerik){
        echo json_encode(array(
            "mesaj" => "lutfen boş kısım bırakmayın ",
            "durum" => 0
        ));
    }else{
        $guncelle=$db->prepare("update posts set
                                title= :title,
                                content= :content
                                where post_id = :post_id and uye_id = :uye_id");
        $guncelle->bindParam(":uye_id",$id);
        $guncelle->bindParam(":post_id",$postId);
        $guncelle->bindParam(":title",$baslik);
        $guncelle->bindParam(":content",$icerik);
        if($guncelle->execute()){
            echo json_encode(array(
                "mesaj" => "Post başarıyla güncellendi",
                "durum" => 1
            ));
        }else{
            echo json_encode(array(
                "mesaj" => $guncelle->errorInfo(),
                "durum" => 0
            ));
        }

    }






}

?>