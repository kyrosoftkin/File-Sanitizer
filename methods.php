<?php
namespace Sanitize\FileMethods;

class Methods
{
    protected $dir;
    protected $files;
    protected $filesNames;
    protected $fileTypes;
    protected $basePath;

    function __construct($path, $types) {
        $this->dir = glob("files/*", GLOB_MARK);
        $this->basePath = $path;
        $this->fileTypes = $types;
    }

    protected function sanitizeName($name) {
      $name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      $name = strtolower($name);

      return $name;
    }

    protected function getFolderName($file) {
        preg_match_all('/[^_]*/', $file, $shortsPaths);

        return $this->sanitizeName($shortsPaths[0][0]);
    }

    protected function getFileType($file) {
      preg_match_all('/[^_]*/', $file, $fileType);

      return $fileType[0][2];
    }

    protected function renameFile($file) {
      $fileType = $this->getFileType($file);

      switch ($fileType) {
        case 'ASO':
          rename($this->basePath.$file, $this->basePath.$this->fileTypes['aso']);
          return $this->fileTypes['aso'];
          break;

        case 'CT':
          rename($this->basePath.$file, $this->basePath.$this->fileTypes['certificado']);
          return $this->fileTypes['certificado'];
          break;

        case 'EPI':
          rename($this->basePath.$file, $this->basePath.$this->fileTypes['epi']);
          return $this->fileTypes['epi'];
          break;

        case 'FR':
          rename($this->basePath.$file, $this->basePath.$this->fileTypes['registro']);
          return $this->fileTypes['registro'];
          break;

        case 'OS':
          rename($this->basePath.$file, $this->basePath.$this->fileTypes['ordem']);
          return $this->fileTypes['ordem'];
          break;

        default:
          $sanitizedFile = $this->sanitizeName($file);

          rename($file, $sanitizedFile);
          return $sanitizedFile;
          break;
      }
    }

    protected function generateFilePaths($files) {
        foreach ($files as $file) {
            $file = str_replace($this->basePath, '', $file);
            $folderName = $this->getFolderName($file);

            $this->filesNames["shortNames"][] = $folderName;
            $this->filesNames["fileNames"][] = $file;
        }
    }

    protected function createFolders() {
        foreach ($this->dir as $file) {
            $newFile = $this->renameFile($file);
            echo 'ARQUIVO: '.$newFile;
            $this->files[] = $newFile;
        }

        $this->generateFilePaths($this->files);

        foreach($this->filesNames["shortNames"] as $shortName) {
            try {
                if(is_numeric($shortName)) {
                  mkdir($this->basePath.$shortName."/Relatorios", 0777, true);
                } else {
                  mkdir($this->basePath.$shortName."/Documentos do Associado", 0777, true);
                }
            } catch (error $e) {
                echo $e;
            }
        }
    }

    public function moveFiles() {
        $this->createFolders();

        foreach($this->filesNames["fileNames"] as $file) {
            $folderName = $this->getFolderName($file);

            try {
              if(is_numeric($folderName)) {
                echo 'NUMERIC FILE: '.$file;
                copy(
                  $this->basePath.$file,
                  $this->basePath.$folderName."/Relatorios/".$file
                );
              } else {
                copy(
                  $this->basePath.$file,
                  $this->basePath.$folderName."/Documentos do Associado/".$file
                );
              }

              $delete[] = $this->basePath.$file;
            } catch (error $e) {
                echo $e;
            }
        }

        foreach ($delete as $file) {
            unlink($file);
            echo $this->getFolderName($file) . " *** done! ***";
        }
    }
}
?>
