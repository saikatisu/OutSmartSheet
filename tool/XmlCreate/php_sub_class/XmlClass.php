<?php

    class XmlClass {
        
       private $log;
       private $env;
       private $dom;
       private $element;
       
        
        // 定義ファイルクラスのinstanceを指定
        public function __construct($log,$env) {
                $this->log = $log;
                $this->env = $env;
                $this->read();
        }
        
        /* 初期処理　xmlファイルを読み込み
         * param : xmlファイルパス($dir)
         * return : なし
         * throw: Exception
         */
        private function read(){
            try {
                
                $this->dom = new DOMDocument('1.0', 'UTF-8');
                //空白ノードを保存
                $this->dom->preserveWhiteSpace = false;
                //字下げや空白を考慮してきれいに整形した出力を行う。
                $this->dom->formatOutput = true;
                
                //xmlを読み込む
                $this->dom->load($this->env["xml"]["dir"]);
                
                //xmlの要素を取得
                $this->element = $this->dom->documentElement;
                
            } catch (Exception $e) {
                throw $e;
            }

        }
        
        /* xmlから要素を削除する
         * param : 要素($array)
         * return : xml内容(String)
         * throw: Exception
         */
        public function deleteElement($array) {
            
            try {
                $root = new DOMNode();
                
                //削除する要素を取得
                foreach ($array as $value) {
                    //sql情報の要素を取得
                    if ($value === reset($array)) {
                        // 最初
                        $root = $this->dom->getElementsByTagName($value)->item(0);
                    }else{
                        $root = $root->getElementsByTagName($value)->item(0);
                    }
                }
                
                //要素を削除する
                $this->element->removeChild($root);
                $this->log->recode("sqlList要素を削除しました");
                
                //削除後のxml内容を返す
                return $this->dom->saveXML();
                
                
            } catch (Exception $e) {
                $this->log->recode("sqlList要素削除に失敗しました");
                throw $e;
            }
        }
        
        /* xmlに要素を追加する
         * param : 追加するノード箇所($array)
         * return : xml内容(String)
         * throw: Exception
         * 
         */
        public function createSqlElement($array ,$fileList){
            
            try {
                //追加するノード箇所を取得
                foreach ($array as $value) {
                    //sql情報の要素を取得
                    if ($value === reset($array)) {
                        // 最初
                        $root = $this->dom->getElementsByTagName($value)->item(0);
                    }else{
                        $root = $root->getElementsByTagName($value);
                    }
                }
                
                //追加要素を作成する
                $itemSqlList = $this->dom->createElement( "sqlList" );
                $root->appendChild( $itemSqlList );
                
                //子要素をSQLファイル数作成する
                foreach ($fileList as $file){
                    //
                    $itemSql = $this->dom->createElement("sql");
                    $itemSqlList->appendChild( $itemSql  );
                    
                    $itemDir = $this->dom->createElement("file");
                    $itemDir ->appendChild(
                        $this->dom->createTextNode(realpath($file))
                        );
                    $itemSql->appendChild( $itemDir );
                    
                    $itemDatabase = $this->dom->createElement("database");
                    $itemDatabase ->appendChild(
                        $this->dom->createTextNode($this->env["runSql"]["database"] )
                        );
                    $itemSql ->appendChild($itemDatabase);
                    
                    $itemType = $this->dom->createElement("type");
                    $itemType ->appendChild(
                        $this->dom->createTextNode($this->env["runSql"]["type"])
                        );
                    $itemSql->appendChild( $itemType );
                    
                    $itemFormat = $this->dom->createElement("format");
                    $itemFormat ->appendChild(
                        $this->dom->createTextNode($this->env["runSql"]["format"] )
                        );
                    $itemSql ->appendChild($itemFormat);
                    
                    $file_sjis = mb_convert_encoding($file, 'cp932', 'UTF-8');
                    setlocale(LC_CTYPE, 'Japanese_Japan.932');
                    $filepath = pathinfo($file_sjis);
                    
                    $itemOutFile = $this->dom->createElement("outFile");
                    $itemOutFile ->appendChild(
                        $this->dom->createTextNode(realpath(dirname($file)) .DIRECTORY_SEPARATOR. "Result_".preg_split('/[.]/', $filepath['basename'])[0].".tsv")
                        );
                    $itemSql ->appendChild($itemOutFile);
                    
                }
                
                $this->log->recode("sqlList要素を新規作成しました");
                
                return $this->dom->saveXML();
                
            } catch (Exception $e) {
                $this->log->recode("sqlList要素の新規作成に失敗しました");
                throw $e;
                
            } 
        }
        
        public function outFile($file){
            
            try {
                
                // ファイルが存在していたら削除
                if (file_exists($file)) {
                    $this->log->recode("既存のxmlファイルを削除しました(outRun用)");
                    unlink($file);
                    
                }
                touch($file);
                $current = file_get_contents($file);
                $current .= $this->dom->saveXML();
                file_put_contents($file, $current);
                $this->log->recode("xmlファイルを作成しました(outRun用)");
                
            } catch (Exception $e) {
                $this->log->recode("xmlファイルの作成に失敗しました(outRun用)");
                throw $e;
            }
            

            
        }
    }

?>