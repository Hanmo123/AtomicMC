<?php
class plugmc {
	function __construct() {
		global $secret,$addr;
		$this->sec = $secret;
		$this->addr = $addr;
	}
	
	function send_post ($type,$para) {
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type:application/x-www-form-urlencoded',
				'content' => http_build_query($para)
			)
		);
		return file_get_contents('http://'.$this->addr.'/?type='.$type, false, stream_context_create($options));
	}
	
	function send($cmd){
		$para=array('key'=>$this->sec,'cmd'=>$cmd);
		$r=json_decode($this->send_post('cmd',$para),true);
		return $r;
	}
	
	function fast_send($cmd){
		$para=array('key'=>$this->sec,'cmd'=>$cmd);
		$this->send_post('fast_send',$para);
		return true;
	}
	
	function send_batch($cmd1,$cmd2,$cmd3,$cmd4){
		$para=array('key'=>$this->sec,'cmd1'=>$cmd1,'cmd2'=>$cmd2,'cmd3'=>$cmd3,'cmd4'=>$cmd4);
        $this->send_post('batch',$para);
		return true;
	}
	
	function say($str){
		$this->fast_send('say '.$str);
		return true;
	}
	
	function tellraw($target,$content){
		$this->fast_send('tellraw "'.$target.'" {"rawtext":[{"text":"'.$content.'"}]}');
		return true;
	}
	
	function title($target,$content){
		$this->fast_send('title "'.$target.'" title '.$content);
		return true;
	}
	
	function subtitle($target,$content){
		$this->fast_send('title "'.$target.'" subtitle '.$content);
		return true;
	}
	
	function get_executor(){
		$this->fast_send('execute @e[type=item,tag=plugmc] ~~~ tag @p add executor');
		return $this->send('testfor @a[tag=executor]')['payload']['victim'];
		$this->fast_send('execute @a[tag=executor] ~~~ tag @s remove executor');
	}
}