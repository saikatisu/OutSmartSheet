<?php

    include './php_sub_class/ReadConfig.php';
    include './php_sub_class/LogClass.php';
    include './php_sub_class/FileList.php';
    include './php_sub_class/XmlClass.php';

    
    //環境変数読み込み
    $readConfig = new ReadConfig();
    $env = $readConfig->getEnvList();

    //ログ出力開始
    $log = new LogClass($env["log"]["dir"]);
    
   $fileList = new FileList();
   $xmlClass = new XmlClass($log,$env);
    
    
    try{  
        //sql情報を削除する
        $xmlClass->deleteElement(array('doc', 'sqlList'));
        
        //サブディレクトリ下のsqlファイルパスを取得する
        $fileList ->showFiles($env["runSql"]["dir"], $env["runSql"]["target"]);
        $dir = $fileList ->getList();
        
        //sql情報の要素を新規作成
        $result=$xmlClass->createSqlElement(array('doc'),$dir);
        
        //xmlファイルを出力する
        $xmlClass->outFile($env["xml"]["dir"]);
        
    }catch(Exception $e){
        echo '捕捉した例外: ',  $e->getMessage(), "\n";
        $log->recode('捕捉した例外: '.  $e->getMessage());
        //異常終了ログを生成
        $log->endRecode(0);

    }

?>