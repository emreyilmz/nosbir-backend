<?php
require_once("../sistem/ayar.php");


if($data){
        //uye ip bul.
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        }

        // TODO resim işlemleri

        $kadi=@strip_tags(trim($data["kadi"]));
        $sifre=@md5(strip_tags(trim($data["sifre"])));
        $eposta=strip_tags(trim($data["eposta"]));

        if(!$kadi ||  !$sifre || !$eposta ){
            echo json_encode(array(
                "mesaj" => "Lutfen tum alanları doldurun.",
                "durum" => 0
                
            ));
        }else if(is_numeric($kadi)){
            echo json_encode(array(
                "mesaj" => "Kullanıcı adı sadece sayılardan oluşamaz.",
                "durum" => 0
                
            ));
        }else if(strlen($kadi)>=15){
            echo json_encode(array(
                "mesaj" => "Lutfen kullanıcı adınızı 15 karakterden büyük yapmayın.",
                "durum" => 0
                
            ));
        }else if(strlen($sifre)<=6){
            echo json_encode(array(
                "mesaj"=>"Şifre 6 karakterden uzun olcak",
                "durum"=>0
            ));
        }else if(!filter_var($eposta,FILTER_VALIDATE_EMAIL)){
            echo json_encode(array(
                "mesaj" => "Lutfen gecerli bir eposta giriniz.",
                "durum" => 0
                
            )); 
        }else{

            // TODO aynı maille giriş var mı kontrol edilecek..
            $mailtekrar=$db->prepare("SELECT * FROM users WHERE  email=:email");
            $mailtekrar->bindParam(":email",$eposta);
            $mailtekrar->execute();
            if($mailtekrar->rowCount()){
                echo json_encode(array(
                    "mesaj" => "Bu eposta adresi zaten kullanımda."
                ));
            }else{


            
            $uyetekrar = $db->prepare("SELECT * FROM users WHERE nick=:nick limit 0,1");
            $uyetekrar->bindParam(":nick",$kadi);
            $uyetekrar->execute();
            $row=$uyetekrar->fetch(PDO::FETCH_ASSOC);
            if ($uyetekrar->rowCount()) {
                echo json_encode(array(
                    "mesaj" =>"Bu kullanıcı adı alınmış.",
                    "durum" => 0
                ));
            }else{
                $iptekrar = $db->prepare("SELECT * FROM users WHERE ip= :ip");
                $iptekrar->bindParam(":ip", $ip);
                $iptekrar->execute();
                $row=$iptekrar->fetch(PDO::FETCH_ASSOC);
                if ($iptekrar->rowCount() > 3) {
                    echo json_encode(array(
                        "mesaj" => "Aynı ip ile daha fazla hesap açamazsınız.",
                        "durum" => 0
                    ));
                }else{

                    $ekle = $db->prepare("INSERT INTO users SET
                    nick=:nick,
                    passwd= :passwd,
                    email= :email,
                    ip= :ip,
                    user_status= :user_status,
                    rank= :rank");
                    $durum = 1;
                    $rutbe = 1;
                    $ekle->bindParam(":nick",$kadi);
                    $ekle->bindParam(":passwd",$sifre);
                    $ekle->bindParam(":email",$eposta);
                    $ekle->bindParam(":ip",$ip);
                    $ekle->bindParam(":user_status", $durum);
                    $ekle->bindParam(":rank", $rutbe);

                    if($ekle->execute()){
                       
                        echo json_encode(array(
                            "mesaj" => "Kayıt başarılı bilgilerinizi girerek oturum açabilirsiniz.",
                            "durum" => 1
                            
                        ));

                    }else{
                        echo json_encode(array(
                            "mesaj" => $ekle->errorInfo(),
                            "durum" => 0
                        ));
                    }
                    
            
                
                }

            }
        }
            
        }

}






?>