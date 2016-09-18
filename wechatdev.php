<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "wechat");

$wechatObj = new wechatCallbackapiTest();

if ($_GET["echostr"]) {
    $wechatObj->valid();
}
else {
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $event = $postObj->Event;
                $msgType = $postObj->MsgType;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

                $newsTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>2</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[欢迎关注superliar]]></Title> 
                            <Description><![CDATA[superliar是一个真人秀节目,balabalabalabalabalabalabalabala]]></Description>
                            <PicUrl><![CDATA[http://i2.hdslb.com/bfs/archive/e4d44a24ef5b838db63ac6c4c5fda0302102fb23.jpg]]></PicUrl>
                            <Url><![CDATA[http://www.panda.tv/]]></Url>
                            </item>
                            <item>
                            <Title><![CDATA[玩狼人杀有什么独家技巧?]]></Title>
                            <Description><![CDATA[一个玩家做了什么不重要，重要的是要知道ta为什么这么做。]]></Description>
                            <PicUrl><![CDATA[http://i0.hdslb.com/bfs/archive/b957f527aadaddc3c32287f0ff5c1858df696658.jpg]]></PicUrl>
                            <Url><![CDATA[https://www.zhihu.com/question/25833846]]></Url>
                            </item>
                            </Articles>
							</xml>";
                $newsNoImgTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>1</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[欢迎关注superliar]]></Title> 
                            <Description><![CDATA[superliar是一个真人秀节目,balabalabalabalabalabalabalabala]]></Description>
                            <Url><![CDATA[http://www.panda.tv/]]></Url>
                            </Articles>
							</xml>";

                if ($event == "subscribe") {
                    echo "订阅事件";
                    $msgType = "news";
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $msgType);
                    echo $resultStr;
                }
                elseif ($event == "unsubscribe") {
                    echo "取消订阅事件";
                }

                if ($msgType == "image") {
                    $msgType = "text";
                    $contentStr = "您发送了image类型的消息,服务器返回text类型消息";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
                elseif ($msgType == "voice") {
                    $msgType = "text";
                    $contentStr = "您发送了voice类型的消息,服务器返回text类型消息";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
                elseif ($msgType == "video") {
                    $msgType = "text";
                    $contentStr = "您发送了video类型的消息,服务器返回text类型消息";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
                elseif ($msgType == "music") {
                    $msgType = "text";
                    $contentStr = "您发送了music类型的消息,服务器返回text类型消息";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }

				if(!empty( $keyword ))
                {
                    if ($keyword == "text") {
                        $msgType = "text";
                        $contentStr = "您发送了".$keyword;
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }
                    if ($keyword == "news") {
                        $msgType = "news";
                        $resultStr = sprintf($newsNoImgTpl, $fromUsername, $toUsername, $time, $msgType);
                        echo $resultStr;
                    }
              		$msgType = "text";
                	$contentStr = "Hello World";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>