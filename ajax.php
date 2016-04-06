<?php
//header("Content-Type: text/html; charset=UTF-8");
require("phpQuery.php");
require("db.php");
$homeurl = "http://www.yoby123.cn/weixin/";
define('ROOT', str_replace("\\", '/', dirname(__FILE__)));
$url= $_GET["url"];
$md5 =md5($url);
$rs = pdo_fetch("select * from ".tablename('wx')." where md5='$md5'");
if(empty($rs)){
$hj = QueryList::Query($url,array("title"=>array('h2#activity-name','text'),
'date'=>array('em#post-date','text'),
'content'=>array('div.rich_media_content','html','-script',function($content){
            $doc = phpQuery::newDocumentHTML($content);
            $imgs = pq($doc)->find('img');
            foreach ($imgs as $img) {
                $src = pq($img)->attr('data-src');
                $type = pq($img)->attr('data-type');
                $filename = time().'-'.md5($src).'.'.$type;
                $localSrc = ROOT.'/w/'.$filename;
                $stream = file_get_contents($src);
                file_put_contents($localSrc,$stream);
                $out = $homeurl.'w/'.$filename;
                pq($img)->attr('src',$out);
                
            }
            return $doc->htmlOuter();
    }),
));
$str = $hj->data;
pdo_insert('wx',array('md5'=>$md5,'title'=>$str[0]['title'],'date'=>$str[0]['date'],'content'=>$str[0]['content']));
echo json_encode($str[0]);
}else{
echo json_encode($rs);

}