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
    $sifre=md5(strip_tags(trim($data["eskiSifre"])));
    $sifreyeni=md5(strip_tags(trim($data["yeniSifre"])));
    
    if(!$sifreyeni){
        echo json_encode(array( 
            "mesaj"=>"Yeni şifreniz 6 karakterden az olamaz", 
            "durum"=>0 )); 
        
    }else if(strlen($sifreyeni)<=6){ 
        echo json_encode(array( 
            "mesaj"=>"Yeni şifreniz 6 karakterden az olamaz", 
            "durum"=>0 )); 
    } 

    $sec=$db->prepare("select * from users where user_id= :user_id limit 1");
    $sec->bindParam(":user_id",$id);
    $sec->execute(); 
    $row=$sec->fetch(PDO::FETCH_ASSOC);

    if($row["passwd"]==$sifre){

        $guncelle=$db->prepare("update users set passwd=:passwd  where user_id= :id");
    
        $guncelle->bindParam(":passwd",$sifreyeni);
        $guncelle->bindParam(":id",$id);
    
        if($guncelle->execute()){
            echo json_encode(array(
                "mesaj" => "Sifre başarıyla güncellendi",
                "durum" => 1
            ));
        }else{
            echo json_encode(array(
                "mesaj" => "Sifre guncellenirken bir hata oluştu",
                "durum" => 0 
            ));
        }

    }else{

        echo json_encode(array(
            "mesaj" => "Sifrenizi yanlış girdiniz.",
            "durum" => 0
        ));

    }
}
?>