<?php
    class SmartSheetApi {
        
        private $authorization;
        private $fileName;
        private $repotrId;
        private $request;
        private $intervalTime;
        
        
        // 定義ファイルクラスのinstanceを指定
        public function __construct($log,$env) {
            
            $this->setAuthorization($env["smart"]["authorization"]);
            $this->fileName=$env["smart"]["fileName"];
            $this->repotrId=$env["smart"]["reportId"];
            $this->request = new RequestClass($env,$log);
            $this->intervalTime = $env["api"]["retry_term"];
            
        }
        
        //認証情報をセット
        private function setAuthorization($authorization) {
            
            $this->authorization = "Bearer ".$authorization;
        }
        
        /*シートをCSVファイルでダウンロード
         * param: シート名
         * 
         */
        public function getSheetData(){
            try {
                $requestUrl ='https://api.smartsheet.com/2.0/reports/' .$this->repotrId;
                
                //ヘッダーを設定
                $headers = array(
                    "AUTHORIZATION:".$this->authorization,
                    "Accept: text/csv",
                    "Content-Type: application/json"
                );
                
                //リクエスト結果を取得
                return $this->request->get_request($requestUrl , $headers,null, $this->fileName);
                
            } catch (Exception $e) {
            }
            
        }
    }



?>