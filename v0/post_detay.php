<?php
//TODO Begeni
// TODO SQL hatalı
require_once("../sistem/ayar.php");
$link=$_GET["link"];
$limit=10;
$post=$db->prepare("select p.post_id,p.title,p.content,p.seo,p.user_id,p.created_at,u.nick,u.picture,g.name,g.group_seo,sum(l.type) as begeni,toplam from posts p 
                    left join (select post_id,COUNT(a.post_id) as toplam from answers a where answer_status=1 GROUP BY a.post_id) a on a.post_id=p.post_id 
                    left join likes l on l.post_id=p.post_id 
                    inner join groups g on p.groups_id=g.group_id
                    inner join users u on p.user_id=u.user_id where p.post_statu=1 and u.user_status=1 and p.seo=:seo
                    group by p.post_id limit 1
                    ");
                    
$post->bindParam(":seo",$link);

if($post->execute()){
    $row=$post->fetch(PDO::FETCH_ASSOC);
    $postId=$row["post_id"];
    

        $postYorum=$db->prepare("select a.text,a.created_at,u.user_id,u.nick,u.picture from answers a,users u
                            where a.post_id=:post_id and a.answer_status=1 and u.user_id=a.user_id 
                            order by a.created_at desc limit $limit");
        
        $postYorum->bindParam(":post_id",$postId);

        if($postYorum->execute()){
            $yorum=$postYorum->fetchAll(PDO::FETCH_ASSOC);

            $yorumSayisi=$db->prepare("select COUNT(*) from answers where post_id=:post_id");
            $yorumSayisi->bindParam(":post_id",$postId);
            $yorumSayisi->execute();
            $sayi=$yorumSayisi->fetch(PDO::FETCH_ASSOC);
            $sayi=ceil($sayi["COUNT(*)"]/$limit);
            echo json_encode(array(
                "durum"=>1,
                "post" => $row,
                "yorum" => array(
                    "data"=>$yorum,
                    "sayi"=>$sayi
                )
            ));
        }else{
            echo json_encode(array(
                "mesaj" => "Yorumlar gonderilirken bir hata oluştu.",
                "durum" => 0
            ));
        }

   
}else{
    echo json_encode(array(
        "mesaj" => "Boyle bir başlık bulunmamaktadır.",
        "durum" => 0
    ));
}


?>