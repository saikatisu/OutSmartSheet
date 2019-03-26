<?php

    class FileList {
        
        private $list=array();
        private $target=".*";
        
        /**
         * 指定したディレクトリとそのサブディレクトリのファイルを表示する
         * @param string $path ディレクトリのパス
         */
        function showFiles($path,$target=".*") {
            
            
            $dir = new DirectoryIterator($path);
            $dirs = array();
            
            $this->target = $target;
            
            foreach ($dir as $file) {
                if ($file->isDot()) //'.'と'..'は表示しない
                
                    continue;
                
                    if ($file->isDir()){
                        $dirs[] = $file->getPathname();
                    }

                    if ($file->isFile() && preg_match('/'. $this->target .'/',$file->__toString ())==1 ){
                        $this->list[] = $file->getPathname();
                    }
                            
            }
            //サブディレクトリのファイルを表示する
            foreach ($dirs as $dir) {
                $this->showFiles($dir,$target);
            }
        }
        
        function getList() {
            return $this->list;
        }
    
    }

?>