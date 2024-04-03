<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-27
 * Time: 오후 6:08
 */
class file
{
    private $makeDirectoryPermission = 0777;

    /**
     * 파일 복사
     * @param $fileData
     * @param $path
     * @param string $copyFileName
     * @return bool
     */
    public function copy($fileData, $path, $copyFileName = "")
    {
        if ($fileData['size'] > 0) {
            $this->makeDirectory($path);
            $fileName = $fileData['name'];
            if (!empty($copyFileName)) {
                $fileName = $copyFileName;
            }
            return copy($fileData['tmp_name'], $path . "/" . $fileName);
        } else {
            return false;
        }
    }

    /**
     * 디렉토리 생성
     * @param $path
     */
    private function makeDirectory($path)
    {
        $dirArray = explode("/", $path);
        $dir = "";
        foreach ($dirArray as $tmpDir) {
            if (!empty($tmpDir)) {
                $dir .= "/" . $tmpDir;
                if (!is_dir($dir)) {
                    mkdir($dir, $this->makeDirectoryPermission);
                    chmod($dir, $this->makeDirectoryPermission);
                }
            }
        }
    }
}