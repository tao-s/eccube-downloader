<?php
/*
 * EC-CUBE CMS simple downloader
 * Copyright 2014 XROSS CUBE, Inc.
 *
 * This script is free to re-distribute as long as you keep
 * this copyright notice.
 *
 * Personal use is permitted.
 *
 * However the copyrighter holds no responsibility against
 * any damages caused by using this script
 * コピーライト以外の改変、再配布OK。個人利用の範囲で勝手に使ってOKです。
 * その代わり当方では一切責任を負いません。
 */
define("SRC_URL","https://downloads.ec-cube.net/src/eccube-4.1.0.zip");
define("VERSION","Ver. 4.1.0");
define("FILENAME","./eccube-4.1.0.zip");
define("DIRNAME","./eccube-4.1.0");

$messages = array(
    "ja" => array(
        "このディレクトリにEC-CUBE ".VERSION." をダウンロードし展開します。",
        "ダウンロード開始",
        "このディレクトリにEC-CUBEの圧縮ファイルをダウンロード中です。",
        "このディレクトリに圧縮ファイルを展開中です。",
        "ダウンロード、展開しました。",
        "ダウンロード、展開に失敗しました。",
        'インストール画面にジャンプします。\nこのファイルは必ず削除してください。',
        "PHPはVer.".phpversion()."を利用していますが、7.1.3以降が必要です。",
        "PHPの必須モジュール「%s」が有効になっていません。サーバの設定を確認してください。"
    ),
    "en" => array(
        "This program will download & deploy EC-CUBE ".VERSION." in this directory.",
        "START",
        "Downloading...",
        "Deploying...",
        "Done!",
        "Oops! Sorry, I couldn't complete the process.",
        'Jump to the install page. \n- Please remove this file -',
        "You use PHP Ver.".phpversion().", require PHP Ver.7.1.3 or later.",
        "Required PHP module '%s' is not enabled. Please check server configuration.",
    )
    //Add translated messages if you want to print your language messages.
);

$required_modules = array(
    "pdo",
    "phar",
    "mbstring",
    "zlib",
    "ctype",
    "session",
    "JSON",
    "xml",
    "libxml",
    "OpenSSL",
    "zip",
    "cURL",
    "fileinfo",
    "intl"
);

ini_set("display_errors",false);

$lang = "en";//Default language
//Check browser accept language
$accept_lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
foreach($accept_lang as $l){
    $l = substr($l,0,2);
    if(array_key_exists($l, $messages)){
        $lang = $l;
        break;
    }
}

//Check PHP env
$env = true;
$error = [];
if( strpos(phpversion(), "7.") !== 0 || strpos(phpversion(), "7.0") === 0 )
{
    $env = false;
    $error[] = $messages[$lang][7]; 
}
foreach($required_modules as $module)
{
    if(!phpversion($module))
    {
        $env = false;
        $error[] = sprintf($messages[$lang][8],$module); 
    }
}

if(isset($_GET["step"]) && $env){
    switch($_GET["step"]){
        case 1:
            $rfp = fopen(SRC_URL, "r");
            $lfp = fopen(FILENAME,"cb");
            if(!$rfp || !$lfp){
                echo json_encode(0);
                exit;
            }else{
                while($line = fgets($rfp)){
                    fwrite($lfp, $line);
                }
                fclose($rfp);
                fclose($lfp);
                echo json_encode(1);
            }
            break;
        case 2:
    		if (function_exists('zip_open')) {
    			try {
    				$zip = new ZipArchive;
    				if ($zip->open(FILENAME) === TRUE) {
    					$zip->extractTo("./");
    					$zip->close();
    					if($dp = opendir("./".DIRNAME)){
        					while (($file = readdir($dp)) !== false) {
                                if ($file != "." && $file != "..") {
                                    rename("./".DIRNAME."/".$file, "./".$file);
                                }
                            }
                            closedir($dp);
    					}
                        exec("rm -rf ./".DIRNAME);
                        exec("rm ./".FILENAME);
    				}
    			} catch(Exception $e) {
    			    echo json_encode(0);
    			    exit;
    			}
                unlink(__FILE__);
				echo json_encode(1);
    			exit;
     		}
            exec("unzip ".FILENAME);
            exec("mv ./".DIRNAME."/* ./");
            exec("rm -rf ./".DIRNAME);
            exec("rm ./".FILENAME);
            unlink(__FILE__);
            echo json_encode(1);
            break;
        default:
            echo json_encode(0);
            break;
    }
}else{
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>EC-CUBE <?php echo VERSION; ?> Downloader</title>
<style>
*{
    box-sizing: border-box;
}
body{
    background: #EEE;
    margin:0;
    padding: 5vh;
    text-align: center;
}
footer{
    padding:21px 0;
    width: 100%;
    bottom: 0;
    color:#333;
    font-size: 80%;
}
img{
    max-width: 90%;
}
a{
    text-decoration: none;
    color:#333;
}
main{
    width:1000px;
    max-width: 100%;
    background: #FFF;
    margin: 0 auto;
    min-height: 80vh;
}
.btn{
    background: #0ba4d7;
    color:#FFF;
    padding: 24px 30px;
    margin:2em auto;
    font-weight: 100;
    font-size: 140%;
    text-decoration: none;
    transition: all 0.4s ease-in-out;
    display: inline-block;
}
.btn:hover{
    background: #0482ac;
}
.step{
    opacity: 0;
    display: none;
    color: #2a5b7f;
    transition: all 0.4s ease-in-out;
    margin-bottom: 1em;
}
#logo{
    width: 24%;   
}
h1{
    color:#666;
    margin-bottom: 0;
}
#version{
    color: #999;
    margin-top: 0;
    margin-bottom: 2em;
}
.error{
    padding: 10px;
    background: #FFD0CC;
    color:#CC1111;
    width:80%;
    margin:1em auto;
}
</style>
</head>
<body>
<main>
<div style="padding-top:10%;"><a href="https://www.ec-cube.net/" target="_blank"><svg id="logo" viewBox="0 0 124.45 105.65"><style>
.cls-1{fill:#353a4e;}.cls-2{fill:#fc0;}.cls-3{fill:#f2b50a;}    
</style><path class="cls-1" d="M106.81,15s6.63,49.2-37.4,62.22c-4.09,0-56.94,6.54-58.6-46.42V81.11L69.4,95,110.81,77V16.58Z"></path><path class="cls-2" d="M110.81,63.42V77L69.4,95,10.81,81.11V65C4,69.18,0,74.22,0,79.65c0,14.36,27.86,26,62.22,26s62.22-11.64,62.22-26C124.45,73.51,119.33,67.86,110.81,63.42Z"></path><path class="cls-2" d="M69.4,0,10.81,25C9,84.13,61.55,78.42,69.4,77.21Z"></path><path class="cls-3" d="M106.81,15,69.4,0V77.21C115.22,65.84,106.81,15,106.81,15Z"></path></svg></a></div>
<h1>EC-CUBE Downloader</h1>
<p id="version">For <?php echo VERSION; ?></p>
<p><?php echo $messages[$lang][0]; ?></p>
<?php if($env){ ?>
<div class="start"><a href="#" class="btn"><?php echo $messages[$lang][1]; ?></a></div>
<?php }else{
    foreach($error as $em){
?>
<p class="error"><?php echo $em; ?></p>
<?php 
    }
} ?>
<p class="step step1"><?php echo $messages[$lang][2]; ?></p>
<p class="step step2"><?php echo $messages[$lang][3]; ?></p>
<p class="step step3"><?php echo $messages[$lang][4]; ?></p>
<p class="step step4 error"><?php echo $messages[$lang][5]; ?></p>
<p class="step loading"><img src="data:image/gif;base64,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" alt="loading..." /></p>
</main>
<footer>copyright &copy; 2018 <a href="http://www.xross-cube.com/">XROSS CUBE, Inc.</a> All Rights Reserved.</footer>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
jQuery(function(){
    $(".btn").click(function(){
        $(".start").hide();
        sendRequest(1);
        return false;
    });
});
function sendRequest(stepNum){
        $(".step"+stepNum).css({"opacity":1,"display":"block"});
        $(".loading").css("opacity",1);
        $.ajax({
            "cache":false,
            "data":{"step":stepNum},
            "dataType":"json",
            "url":"./dw.php",
            "success":function(d,t){
                if(d == 1 ){
                    $(".step"+(stepNum+1)).css({"opacity":1,"display":"block"});
                    if(stepNum <= 1){
                        sendRequest((stepNum+1));
                    }else{
                        $(".loading").css("opacity",0);
                        setTimeout('gotoInstallPage()', 1000);
                    }
                }else{
                    $(".loading").css("opacity",0);
                    $(".step4").css({"opacity":1,"display":"block"});
                }
            },
            "error":function(){
                $(".loading").css("opacity",0);
                $(".step4").css({"opacity":1,"display":"block"});
            }
        });
}
function gotoInstallPage(){
        location.href="./index.php";
}
</script>
</body>
</html><?php } ?>
