<?php
require_once("../sistem/ayar.php");
if(isset($_GET)){

    $durum=isset($_GET["durum"]) ? $_GET["durum"] : 1;
    //Tum zamanlar
    if($durum==1){
    
        $listele=$db->prepare("select p.title,p.seo,u.nick,u.picture,g.name,g.group_seo,count(a.post_id) ys from posts p
                            left join answers a on a.post_id=p.post_id
                            left join users u on u.user_id=p.user_id
                            left join groups g on g.group_id=p.groups_id
                            group by p.post_id
                            order by ys desc
                            limit 5");
        
        if($listele->execute()){
          
            $row=$listele->fetchAll(PDO::FETCH_ASSOC);
            
            if($row){
                echo json_encode(array(
                    "post"=> $row,
                    "durum"=>1
                ));
                
            }else{
                echo json_encode(array(
                    "mesaj" => "Bugün popüler nos yok.Neden senin nosun popüler olmasın.Hadi Nosla",
                    "durum" => 0
                ));
            }
        }else{
            echo json_encode(array(
                "mesaj" => "Tum zamanların en populer 10 nosu listelenirken bir sorun oluştu.",
                "durum" => 0
            ));
            
        }
    
    
    //Bugün
    }else if($durum==0){
        //TODO INTERVAL kısmı :DATE_SUB(CURDATE(), INTERVAL 0 DAY)
        $listele=$db->prepare("select *,count(a.post.id) ys from posts
                                left join answer a on a.post_id=p.post_id
                                where post.created_at >= curdate()
                                group by p.post_id
                                order by ys desc 
                                limit 10");


        if($listele->execute()){

            $row=$listele->fetchAll(PDO::FETCH_ASSOC);


            if($row){
                echo json_encode(array(
                    "data" => $row
                ));
            }else{  

                // TODO bir önceki günlere bakarak limite tamamla

                $listele=$db->prepare("select *,count(a.post_id) ys from posts p
                                left join answer a on a.post_id=p.post_id
                                where post.created_at >= date_sub(curdate(),interval 1 day)
                                group by p.post_id
                                order by ys desc 
                                limit 10");
                
                if($listele->execute()){;
                    $row=$listele->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(array(
                        "data" => $row
                    ));
                }else{

                }
            }
        }else{
            echo json_encode(array(
                "mesaj" => "Bugunun en populer 10 postu listelenirken bir sorun oluştu",
                "durum" => 0
            ));
        }
    }

}


















?>