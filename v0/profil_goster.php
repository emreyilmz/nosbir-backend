<?php
require_once("../sistem/ayar.php");
if (isset($_GET)) {
    $kadi=$_GET["kadi"]; 

    $sayfa=@$_GET["s"] ? @$_GET["s"] : 1;
    $limit=10;
    $offset=($sayfa-1)*$limit;

    $kullanici=$db->prepare("select user_id,nick,picture from users where nick = :nick");
    $kullanici->bindParam(":nick",$kadi);

    if($kullanici->execute()){

        $kullanici=$kullanici->fetch(PDO::FETCH_ASSOC);

        $sorgu=$db->prepare("select p.post_id,p.title,p.content,p.seo,p.created_at,u.nick,u.picture,g.name,g.group_seo,sum(l.type) as begeni,toplam from posts p 
                            left join (select post_id,COUNT(a.post_id) as toplam from answers a GROUP BY a.post_id) a on a.post_id=p.post_id 
                            left join likes l on l.post_id=p.post_id 
                            inner join groups g on p.groups_id=g.group_id
                            inner join users u on p.user_id=u.user_id where p.post_statu=1 and u.user_status=1 
                            and u.user_id= :id
                            group by p.post_id
                            order by created_at desc
                            limit $offset,$limit");

        $sorgu->bindParam(":id",$kullanici["user_id"]);

        if($sorgu->execute()){
            $uyevepost=$sorgu->fetchAll(PDO::FETCH_ASSOC);


            $postSay=$db->prepare("select COUNT(*) from posts p,users u where u.user_id=p.user_id and u.user_id= :id");
            $postSay->bindParam(":id",$kullanici["user_id"]);

            if($postSay->execute()){
                $row=$postSay->fetch(PDO::FETCH_ASSOC);
                echo json_encode(array(
                    "durum"=>1,
                    "uye" => $kullanici,
                    "post" => $uyevepost,
                    "toplamPost" => $row["COUNT(*)"]
                    
                ));

            }else{

                echo json_encode(array(
                    "mesaj" => "Post sayısı hesaplanırken bir sorun oluştu",
                    "durum" => 0
                ));
                
            }
        }else{

            echo json_encode(array(
                "mesaj" => "Uye bilgileri getirilirken bir sorun oluştu",
                "durum" => 0
            ));

        }

    }else{
        echo json_encode(array(
            "mesaj" => "Uye bilgileri getirilirken bir sorun oluştu",
            "durum" => 0
        ));
    }





}
?>