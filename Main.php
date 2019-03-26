<?php

    include './php_sub_class/ReadConfig.php';
    include './php_sub_class/LogClass.php';
    include './php_sub_class/SmartSheetApi.php';
    include './php_sub_class/RequestClass.php';
    include './php_sub_class/CsvRead.php';
    include './php_sub_class/CreateDir.php';
    

    
    //環境変数読み込み
    $readConfig = new ReadConfig();
    $env = $readConfig->getEnvList();
    
    //実行パラメータがある場合
    if($argc > 1){
        $env["createDir"]["month"]=$argv[1];
    }
    //ログ出力開始
    $log = new LogClass($env["log"]["dir"]);
    
    
    //スマートシートAPI初期化
    $api = new SmartSheetApi($log,$env);
    $read = new CsvRead($log,$env);
    $createDir = new CreateDir($log,$env);
    
    
    try{
        //レポートファイルをダウンロードする
        $api->getSheetData();
        
        //レポートファイルを読み込み
        $data=$read->readFile($env["smart"]["fileName"]);
        
        //アドホックメール作業ディレクトリを作成する
        $createDir->createWorkDir($data);
        

    }catch(Exception $e){
        echo '捕捉した例外: ',  $e->getMessage(), "\n";
        $log->recode('捕捉した例外: '.  $e->getMessage());
        //異常終了ログを生成
        $log->endRecode(0);

    }

?>