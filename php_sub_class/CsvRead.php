<?php

    class CsvRead {
        
        private $log;
        private $env;
        
        /*　コンストラクタ
         *   param: 環境変数、LogClass
         */
        function __construct($log,$env) {
           
            $this->log = $log;
            $this->env = $env;
        }
        
        /*　CSVファイルを読み込む
         * 　pram: フォルダパス
         *   return: Boolean
         *   throw: Exception
         */
        public function readFile($path) {
            try{
                
                if(file_exists ( $path )){
                    $file = new SplFileObject( $path );
                    foreach ($file as $line) {
                        $data[] = str_getcsv($line);
                        
                    }
                    return $data;
                }else {
                    throw new Exception("Not File Error");
                }
            }catch(Exception $e){
                throw $e;
            }
            
        }
        
    }
    
    

?>