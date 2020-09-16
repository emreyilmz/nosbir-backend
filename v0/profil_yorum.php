<?php
//Kullanıcıya ait yorumları listeler.
require_once("../sistem/ayar.php");

if (isset($_GET)) {

    $kadi=$_GET["kadi"]; 
    $sayfa=@$_GET["s"] ? @$_GET["s"] : 1;
    $limit=10;

    $offset=$sayfa*$limit;

    $kullanici=$db->prepare("select user_id,nick,picture from users where nick = :nick");
    $kullanici->bindParam(":nick",$kadi);

    if($kullanici->execute()){

        $kullanici=$kullanici->fetch(PDO::FETCH_ASSOC);

        $sorgu=$db->prepare("select a.text,a.created_at,u.user_id,u.nick,u.picture from answers a,users u,posts p
                            where a.post_id=p.post_id and a.answer_status=1 and u.user_id=a.user_id and u.user_id=:id
                            order by a.created_at desc limit $offset,$limit");

        $sorgu->bindParam(":id",$kullanici["user_id"]);

        

        if($sorgu->execute()){
            $yorum=$sorgu->fetchAll(PDO::FETCH_ASSOC);

            $toplamYorum=$db->prepare("select COUNT(*) from answers a,users u where a.user_id=u.user_id and u.user_id= :id");
            $toplamYorum->bindParam(":id",$kullanici["user_id"]);
            $toplamYorum->execute();
            $toplam=$toplamYorum->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array(
                "yorumlar"=>$yorum,
                "uye"=>$kullanici,
                "toplamYorum"=>$toplam["COUNT(*)"],
                "durum"=>1
            ));
        }else{
            echo json_encode(array(
                "mesaj" =>"Kullanıcıya ait mesajlar listelenirken bir sorun oluştu",
                "durum" =>0

            ));
        }
    } else{
        echo json_encode(array(
            "mesaj" => "Uye bilgileri getirilirken bir sorun oluştu",
            "durum" => 0
        ));
    }










}
?>