<?php
$victim=$plugmc->get_executor()[0];
switch($para[1]){
	case 'help':
		$plugmc->tellraw($victim,'§b§l[Yum] §r§b插件管理器 §l帮助列表\n§ayum list §e查看本地插件列表\n§ayum info [插件名] §e从本地获取插件的信息\n§ayum install [插件名] §e从PlugMC下载插件\n§ayum uninstall [插件名] §e从本地卸载插件');
		break;
	case 'list':
		$r=scandir('plugins/');
		$content='§b§l[Yum] §r§b插件管理器 §l插件列表';
		foreach($r as $p){
			if($p!='.' and $p!='..'){
				include 'plugins/'.$p.'/config.php';
				$content=$content.'\n§a插件目录 '.$p.' §e插件名 '.$info['name'].' 作者 '.$info['author'].' 版本 '.$info['version'];
			}
		}
		$plugmc->tellraw($victim,$content);
		break;
	case 'info':
		if(is_file('plugins/'.$para[2].'/config.php')){
			include 'plugins/'.$para[2].'/config.php';
			$plugmc->tellraw($victim,'§b'.$info['name'].' §e'.$info['intro']);
		}else{
			$plugmc->tellraw($victim,'§c[Yum] 插件不存在');
		}
		break;
	case 'install':
		if($per[$victim]==1){
			$plugmc->tellraw($victim,'§b§l[Yum] §r§a正在从官网获取插件');
			exec('cd plugins/yum/temp/ && curl -O "https://atom.mcplugin.cn/plugmc/downloads/PlugMC-latest.tar.gz"',$yumr);
			if($yumr[0]=='<html>'){
				$plugmc->tellraw($victim,'§b§l[Yum] §r§e插件不存在');
			}else{
				$plugmc->tellraw($victim,'§b§l[Yum] §r§a插件下载成功，正在解压');
				exec('cd plugins/yum/temp/ && tar -xzvf PlugMC-latest.tar.gz');
				$plugmc->tellraw($victim,'§b§l[Yum] §r§a解压插件成功，正在安装');
				foreach(scandir('plugins/yum/temp/reg_cmd/') as $row){
					if($row!='.' and $row!='..'){
						rename('plugins/yum/temp/reg_cmd/'.$row,'reg_cmd/');
					}
				}
				mkdir('plugins/'.$para[2]);
				foreach(scandir('plugins/yum/temp/main/') as $row){
					if($row!='.' and $row!='..'){
						rename('plugins/yum/temp/main/'.$row,'plugins/');
					}
				}
				$plugmc->tellraw($victim,'§b§l[Yum] §r§a解压安装成功');
			}
			unset($r);
		}else{
			$plugmc->tellraw($victim,'§b§l[Yum] §r§c您的权限不足');
		}
		break;
	default:
		$plugmc->tellraw($victim,'§b§l[Yum] §r§a插件管理器\n§c错误: 缺少参数\n§c使用§eyum help§c获取帮助');
		break;
}