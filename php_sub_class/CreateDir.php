<?php

include './php_sub_class/FileHistory.php';

// class CreateDir extends FileHistory{
 class CreateDir extends FileHistory{
        
        const  BANSTR= array("/" ,"\\" ,"*","?" ,"<",">","|" ,"\"","\r\n", "\r", "\n");
        private $log;
        private $env;
        private $filehistory;
        
        /*　コンストラクタ
         *   param: 環境変数、LogClass
         */
        function __construct($log,$env) {
            $this->log = $log;
            $this->env = $env;
            $this->filehistory=new FileHistory();
        }
        
        /*　フォルダ存在チェック
         * 　pram: ファイルパス(Araay型[String])
         *   return: Boolean
         *   throw: Exception
         */
        public function fileCheck($file_list){
            
            try{
                
                //引数の型を確認
                if(is_array($file_list)){
                    foreach ($file_list as $file) {
                        
                        if(!file_exists ( $file )){
                            return FALSE;
                        }
                    }
                    
                }else{
                    throw new Exception("Not Array Error");
                }
                return TRUE;
                
            }catch(Exception $e){
                throw $e;
            }
            
        }
        
        /*　フォルダ作成
         * 　pram: フォルダパス(Araay型[String])
         *   return: Boolean
         *   throw: Exception
         *
         *   既にフォルダが存在している場合は作成しない
         */
        public function folderMake($folder) {
            try{
                
                //フォルダ存在確認
                if(!$this->fileCheck(array($folder)) ){
                    return mkdir($folder, 0777,true);
                    
                }else{
                    //フォルダが既に存在している
                    return TRUE;
                }
                
                
            }catch(Exception $e){
                throw $e;
            }
            
        }

        /* アドホックメール作業フォルダを作成する
         *  param: アドホックメール情報(Hash型) 
         */
        public function createWorkDir($data){
            
            //作成する来月・今月フォルダかを判定
            $month=date('Ym', strtotime($this->env["createDir"]["month"] .' month'));
            $this->log->recode("実行対象月：　".$month);
            
            //優先度採番用変数
            $i = 1;
            $t = 1;
            
            //アドホックメール数分ループ
            foreach ($data as $recode) {

                //最初・最後の要素以外
                if ($recode <> reset($data) && $recode <> end($data)) {
                    
                    $date = new DateTime(substr($recode[1] ,-8,2) . "-" .substr($recode[1] ,-5,2). "-" .substr($recode[1] ,-2));
                    
                    //日付の判定
                    if($month == $date->format('Ym')) {
                        //フォルダ禁止名を除外
                       $name=str_replace(self::BANSTR, "", $recode[3]);
                       
                       //優先度処理の除外対象
                       str_replace(array("重複可", "お気に入り","CPB専門店","エリアメール"), "", $name, $count);
                       
                       //日付フォルダ名作成
                       $dirName = $this->env["createDir"]["dir"] .DIRECTORY_SEPARATOR. $date->format('Ymd');
                       
                       //日付フォルダ存在判定
                       if(!file_exists($dirName)){
                           
                           //優先度を初期化
                           $i = 1;
                           $t = 1;
                           
                           //日付フォルダを作成
                           $this->folderMake($dirName);
                           
                       }
                       
                       //フォルダ名を作成
                       if($count != 0){
                           $dirMail = $dirName  .DIRECTORY_SEPARATOR ."重複可_". $t . DIRECTORY_SEPARATOR  ;
//                            $name = $dirName  .DIRECTORY_SEPARATOR ."重複可_". $t . DIRECTORY_SEPARATOR . $date->format('Ymd') ."_" . $t ."_". $name;
                           $name = $dirMail . $date->format('Ymd') ."_" . $t ."_". $name;
                           $t++;
                       }else{
                           $dirMail  = $dirName  .DIRECTORY_SEPARATOR . "アドホックメール" . DIRECTORY_SEPARATOR ;
//                            $name = $dirName  .DIRECTORY_SEPARATOR . "アドホックメール" . DIRECTORY_SEPARATOR . $date->format('Ymd') ."_" . $i ."_". $name;
                           $name = $dirMail . $date->format('Ymd') ."_" . $i ."_". $name;
                           $i++;
                           
                       }
                       //フォルダを作成
                       $this->folderMake($name);
                       
                       //自動化用ファイルをコピー
                       $this->fileCopy(getcwd().DIRECTORY_SEPARATOR . "tool". DIRECTORY_SEPARATOR  , $dirMail .DIRECTORY_SEPARATOR, "*");
                       
                       
                       $this->dir_copy(getcwd().DIRECTORY_SEPARATOR ."tool".DIRECTORY_SEPARATOR . "TOTAL" . DIRECTORY_SEPARATOR  ,$dirMail .DIRECTORY_SEPARATOR. "TOTAL");
//                        $this->folderMake($dirMail .DIRECTORY_SEPARATOR. "TOTAL");
//                        $this->fileCopy(getcwd().DIRECTORY_SEPARATOR ."tool".DIRECTORY_SEPARATOR . "TOTAL" . DIRECTORY_SEPARATOR  ,$dirMail .DIRECTORY_SEPARATOR. "TOTAL" , "*");
                       $this->dir_copy(getcwd().DIRECTORY_SEPARATOR ."tool". DIRECTORY_SEPARATOR ."XmlCreate" . DIRECTORY_SEPARATOR   ,$dirMail .DIRECTORY_SEPARATOR. "XmlCreate");
//                        $this->folderMake($dirMail .DIRECTORY_SEPARATOR. "XmlCreate");
//                        $this->fileCopy(getcwd().DIRECTORY_SEPARATOR ."tool". DIRECTORY_SEPARATOR ."XmlCreate" . DIRECTORY_SEPARATOR   ,$dirMail .DIRECTORY_SEPARATOR. "XmlCreate". "*");
                       $this->dir_copy(getcwd().DIRECTORY_SEPARATOR . "tool". DIRECTORY_SEPARATOR ."メールテンプレート" . DIRECTORY_SEPARATOR    ,$dirMail .DIRECTORY_SEPARATOR. "メールテンプレート");
//                        $this->folderMake($dirMail .DIRECTORY_SEPARATOR. "メールテンプレート");
//                        $this->fileCopy(getcwd().DIRECTORY_SEPARATOR . "tool". DIRECTORY_SEPARATOR ."メールテンプレート" . DIRECTORY_SEPARATOR    ,$dirMail .DIRECTORY_SEPARATOR. "メールテンプレート", "*");

                       //ファイルコピー(依頼書をコピーする）
                       if($this->env["fileCopy"]["flg"] > 0 ){
                           
                           $this->log->recode($name);
                           $this->log->recode($this->env["fileCopy"]["dir"]  .DIRECTORY_SEPARATOR . "【".substr($recode[2] ,0,7) . "】".DIRECTORY_SEPARATOR .$recode[2], $name ,$recode[2]);
                           $this->fileCopy($this->env["fileCopy"]["dir"]  .DIRECTORY_SEPARATOR . "【".substr($recode[2] ,0,7) . "】".DIRECTORY_SEPARATOR .$recode[2], $name ,$recode[2] . "*");

                       }
                      
                    }
                }

                
            }
            
        }
        
    }
    

?>