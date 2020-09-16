<?php
require_once("../sistem/ayar.php");

$sayfa=@$_GET["s"] ? @$_GET["s"] : 1;
$limit=10;

$offset=$sayfa*$limit;

    $post_id=$_GET["id"];

    $postYorum=$db->prepare("select a.text,a.created_at,u.user_id,u.nick,u.picture from answers a,users u,posts p
    where a.post_id=p.post_id and a.answer_status=1 and u.user_id=a.user_id and p.seo=:post_id
    order by a.created_at desc limit $offset,$limit");

    $postYorum->bindParam(":post_id",$post_id);

    if($postYorum->execute()){
        $yorum=$postYorum->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array(
            "durum"=>1,
            "yorum" => $yorum,
        ));

    }else{
        echo json_encode(array(
            "mesaj"=>"Yorumlar gönderilirken bir sorun oluştu",
            "durum" => 0
        ));
    }






?>