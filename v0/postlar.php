<?php
//TODO Begeni
// TODO SQL hatalı
require_once("../sistem/ayar.php");

$sayfa=@$_GET["s"] ? @$_GET["s"] : 0;
$limit=10;
if($sayfa<0){
    $sayfa=0;
}
$offset=$sayfa*$limit;
if(!isset($_GET["topluluk"])){
    $sorgu=$db->prepare("select p.post_id,p.title,p.content,p.seo,p.created_at,u.nick,u.picture,g.name,g.group_seo,sum(l.type) as begeni,toplam from posts p 
                        left join (select post_id,COUNT(a.post_id) as toplam from answers a GROUP BY a.post_id) a on a.post_id=p.post_id 
                        left join likes l on l.post_id=p.post_id 
                        inner join groups g on p.groups_id=g.group_id
                        inner join users u on p.user_id=u.user_id where p.post_statu=1 and u.user_status=1 
                        group by p.post_id
                        order by p.post_id desc
                        limit $offset,$limit");


}else{
    $topluluk=$_GET["topluluk"];
    $sorgu=$db->prepare("select p.post_id,p.title,p.content,p.seo,p.created_at,u.nick,u.picture,g.name,g.group_seo,sum(l.type) as begeni,toplam from posts p 
                        left join (select post_id,COUNT(a.post_id) as toplam from answers a GROUP BY a.post_id) a on a.post_id=p.post_id 
                        left join likes l on l.post_id=p.post_id 
                        inner join groups g on p.groups_id=g.group_id
                        inner join users u on p.user_id=u.user_id where p.post_statu=1 and u.user_status=1 
                        and g.group_seo=:group_seo
                        group by p.post_id
                        order by p.post_id desc
                        limit $offset,$limit");


                        $sorgu->bindParam(":group_seo",$topluluk);


}

    if($sorgu->execute()){
        $basliklar=$sorgu->fetchAll(PDO::FETCH_ASSOC);

        if(!isset($_GET["topluluk"])){
            $toplamSayfa=$db->prepare("select COUNT(*) from posts");
        }else{
            $topluluk=$_GET["topluluk"];
            $toplamSayfa=$db->prepare("select COUNT(*) from posts p,groups g 
                                    where p.groups_id=g.group_id and group_seo=:group_seo");
            
            $toplamSayfa->bindParam(":group_seo",$topluluk);
        }
        $toplamSayfa->execute();
        $toplam=$toplamSayfa->fetch(PDO::FETCH_ASSOC);
        $sayfa=ceil($toplam["COUNT(*)"]/$limit);
        echo json_encode(array(
            "durum"=>1,
            "postlar"=>$basliklar,
            "toplam"=>$sayfa
        ));
    }else{
        echo json_encode(array(
            "durum"=>0,
            "mesaj"=>"Teknik bir sorun oluştu"
        ));
    }



?>