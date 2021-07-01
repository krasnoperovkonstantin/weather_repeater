<?php
  include "db_connect.php"; 
  
  $res = $link->query("SELECT * FROM display where NOW() > datetime + INTERVAL 60 MINUTE and id = 1 ");

  while ($row = $res->fetch_assoc())
  {
    $opts = array(
          'http' => array(
            'method' => "GET",
            'header' => "X-Yandex-API-Key: ------------------------------"
          )
        );
    $url = "https://api.weather.yandex.ru/v2/informers?lat=57.051806&lon=53.987392&lang=ru_RU";  //57.051806, 53.987392
    $context = stream_context_create($opts);
    $contents = file_get_contents($url, false, $context);

    $clima = json_decode($contents);
    $wing_speed=strval(intval($clima->fact->wind_speed));
    $query= "UPDATE `display` SET 
    `datetime` = now(),
    `part_name`='fact',
    `temp`='{$clima->fact->temp}',
    `humidity`='{$clima->fact->humidity}',
    `wing_speed`='{$wing_speed}',
    `wing_dir`='{$clima->fact->wind_dir}',
    `condition`='{$clima->fact->condition}'
    WHERE id=1";
    $link->query($query);

    $wing_speed=strval(intval($clima->forecast->parts[0]->wind_speed));
    
    $query= "UPDATE `display` SET 
    `datetime` = now(),
    `part_name`='{$clima->forecast->parts[0]->part_name}',
    `temp`='{$clima->forecast->parts[0]->temp_avg}',
    `humidity`='{$clima->forecast->parts[0]->humidity}',
    `wing_speed`='{$wing_speed}',
    `wing_dir`='{$clima->forecast->parts[0]->wind_dir}',
    `condition`='{$clima->forecast->parts[0]->condition}'
    WHERE id=2";
    $link->query($query);

    $wing_speed=strval(intval($clima->forecast->parts[1]->wind_speed));
    
    $query= "UPDATE `display` SET 
    `datetime` = now(),
    `part_name`='{$clima->forecast->parts[1]->part_name}',
    `temp`='{$clima->forecast->parts[1]->temp_avg}',
    `humidity`='{$clima->forecast->parts[1]->humidity}',
    `wing_speed`='{$wing_speed}',
    `wing_dir`='{$clima->forecast->parts[1]->wind_dir}',
    `condition`='{$clima->forecast->parts[1]->condition}'
    WHERE id=3";
    $link->query($query);  
  }
  $res = $link->query("SELECT * FROM `display` ");
  
  $display= array ();

  while ($row = $res->fetch_assoc())
  {
      foreach ($row as $key => $value) {
          $display[$row['id'].$key]=$value;
      }
  }
  $display['time']=date('H:i', time()+60*60);
  $display['hour']=date('H', time()+60*60);
  $display['min']=date('i', time()+60*60);
  $display['day']=date('j', time()+60*60);
  $display['ndw']=date('N', time()+60*60);
  $display['delay']="20";
  echo json_encode ($display);
       