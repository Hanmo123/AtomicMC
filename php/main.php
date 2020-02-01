<?php
$secret='123456789@#';
$addr='0.0.0.0:23334';

require_once('system/include/miscellaneous.php');
require_once('system/class/main.php');

$per=json_decode(file_get_contents('system/permissions.json'),true);
$plugmc=new plugmc();

while(true){
    $plugmc->fast_send('execute @e[type=item] ~~~ detect ~~-1~ jukebox 0 tag @s add plugmc');
    $r=$plugmc->send('testfor @e[type=item,tag=plugmc]');
    if($r['code']==200){
        if($r['payload']['statusMessage']!='没有与选择器匹配的目标'){
            foreach($r['payload']['victim'] as $target){
            	$para=explode(' ',$target);

                if(is_file('reg_cmd/'.$para[0].'.php')){
                    include 'reg_cmd/'.$para[0].'.php';
                    if(is_file('plugins/'.$info['plugin'].'/main/'.$info['file'])){
                    	include 'plugins/'.$info['plugin'].'/main/'.$info['file'];
                    	echo '['.date("m-d H:i:s").",System] 收到命令[{$target}]执行成功\n";
                    }else{
                    	echo '['.date("m-d H:i:s").",System] 收到命令[{$target}] 但没有找到对应的可执行文件\n";
                    }
                }else{
                    echo '['.date("m-d H:i:s").",System] 收到命令[{$target}] 但没有找到已注册的命令\n";
                }
                unset($target);
            }
            unset($r);
            $plugmc->fast_send('kill @e[type=item,tag=plugmc]');
        }
    }else{
        die('['.date("m-d H:i:s").",System] Websocket密钥或状态错误.");
    }
    $plugmc->fast_send('execute @e[type=item,tag=plugmc] ~~~ tag @s remove plugmc');

    usleep(1000000);
}