RedisFullTextSearchCn
=====================

chinese full test search engine  for redis .   it's able to fuzzy search .  

this is the class with PHP

before use the class,you must to install two php extends php_redis and php_scws .

For example:

$r=new MyRedisSc();
$r->to_fc('test',"这是测试分词搜索",2);
$r->to_fc('test',"我要测试另外一个",3);  
$r->search("测试","test");
print_r($data);

//print reslut:
// array(2,3)
