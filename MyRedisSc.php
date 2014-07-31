<?php 
/**
**  create by tanghui 
**  create datetime 2014/7/23 14:00
**/
class MyRedisSc{
	var $cws; 
	var $redis;

	function __construct(){
	  
	  $CI =& get_instance();
	 
	  $this->redis = new Redis();
      	  $this->redis->connect("127.0.0.1"); //the host ip  
          $this->redis->select(0);
	  $this->cws=scws_new();
	  $this->cws->set_charset('utf8');
	  $this->cws->set_dict(dirname(__FILE__).'/../../scws/dict.utf8.xdb');
	  $this->cws->set_rule(dirname(__FILE__).'/../../scws/rules.utf8.ini');
	  $this->cws->set_ignore(true);
	  $this->cws->set_duality(false);
	  $this->cws->set_multi(8);
	  
	}
	
	function to_fc($mkey,$content,$postid,$single=false)
	{
	    if(empty($content)){
            return true;
        }
		if($single)
		{
			$this->redis->zAdd("{$mkey}:word:{$content}",1,$postid);
			return true;
		}
		
		$this->cws->send_text(strip_tags($content));
        while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $this->redis->zAdd("{$mkey}:word:{$tmp['word']}",1,$postid);
                }
            }
        }
        return true;
	   
	}
	
	function search($key,$mkey,$single=false,$limit=0,$num=-1)
	{
	    if(empty($key)){
            return true;
        }
		if($single)
		{
		  $data = $this->redis->zRevRange("{$mkey}:word:{$key}", $limit , $num);
		  return $data;
		}
		$this->cws->send_text($key);
	    $keyarray = array();
		 while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $keyarray[] = "{$mkey}:word:{$tmp['word']}";
                }
            }
        }
		$this->redis->zInter('result', $keyarray, array_fill(0, count($keyarray) , 1));
		$data = $this->redis->zRevRange('result', $limit , $num);
	    return $data;
	}
	
	function delOrdKey($mkey,$content,$postid,$single=false)
	{
	    if(empty($content)){
            return true;
        }
		if($single)
		{
		   $this->redis->zrem("{$mkey}:word:{$content}",$postid);
		}
		
		$this->cws->send_text($content);
        while ($res = $this->cws->get_result()){
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r"){
                    continue;
                }elseif ($tmp['len'] == 1 && $tmp['word'] == "\n"){
                    continue;
                }else{
                    $this->redis->zrem("{$mkey}:word:{$tmp['word']}",$postid);
                }
            }
        }
        return true;
		
	}
	
	

}
