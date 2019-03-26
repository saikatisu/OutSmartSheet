<?php

class FileHistory {
    
    
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
                return mkdir($folder, 0777);
                
            }else{
                //フォルダが既に存在している
                return TRUE;
            }

            
        }catch(Exception $e){
            throw $e;
        }
        
    }
    
    
    /*　ファイルコピー
     * 　pram: コピー元パス（$source）、コピー先パス（$dest）,ファイル名($target)
     *   return: Boolean
     *   throw: Exception
     *   
     *   デフォルトはすべてのファイルをコピーする
     *   特定ファイルのみの場合は$targetで指定する
     */
    public function fileCopy($source , $dest , $target="*") {
        try {
            //フォルダ存在確認
            if($this->fileCheck(array($source , $dest))){
                
                //ファイル数だけループ処理
                if(count(glob($source  .DIRECTORY_SEPARATOR . $target)) > 0){
                    
                    foreach(glob($source  .DIRECTORY_SEPARATOR . $target) as $file){
                        
                        if(is_file($file)){
                            //ファイルをコピー
                            copy($file, $dest .DIRECTORY_SEPARATOR .basename($file));
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /*　ファイルコピー
     * 　pram: コピー元パス（$dir_name）、コピー先パス（$new_dir）
     *   return: Boolean
     *   throw: Exception
     *
     *  指定フォルダをすべてコピーする
     */
    function dir_copy($dir_name, $new_dir){
        if (!is_dir($new_dir)) {
            mkdir($new_dir);
        }
        
        if (is_dir($dir_name)) {
            if ($dh = opendir($dir_name)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == "." || $file == "..") {
                        continue;
                    }
                    if (is_dir($dir_name . "/" . $file)) {
                        $this->dir_copy($dir_name . "/" . $file, $new_dir . "/" . $file);
                    }
                    else {
                        copy($dir_name . "/" . $file, $new_dir . "/" . $file);
                    }
                }
                closedir($dh);
            }
        }
        return true;
    }

}

?>