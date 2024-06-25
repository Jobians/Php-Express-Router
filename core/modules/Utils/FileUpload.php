

<?php


class FileUpload
{


    public $uploadDir = null;

    private array $allowedExt = [];

    private array $uploadedSucessFiles = [];

    public function __construct(
        public  $uploadedFiles,
        public $isMultiple
    ) {
        var_dump($uploadedFiles);
    }


    /**
     * performs file upload and provide a callback function to handle upload results
     * @param callable $callback Callback function to execute after each upload
     * @param callable $naming Optional function to call in order to change upload file name
     * @return bool
     */


    public function upload(callable $callback ,  callable $naming = null)
    {

        if ($this->uploadDir) {
            if ($this->isMultiple) {
                for ($file = 0; $file < count($this->uploadedFiles['name']); ++$file) {

                    $temp_name = $this->uploadedFiles['tmp_name'][$file];
                    $uploadFilename = $this->uploadedFiles['name'][$file];
                    $uploadMime = $this->uploadedFiles['type'][$file];
                    if (is_uploaded_file($temp_name)) {

                        if ($this->allowedExt and !$this->isAllowedExt($uploadMime)) {
                            trigger_error("File extention not allowed");
                            return false;
                        } else {

                            if(is_callable($naming)) {
                                $uploadFilename = $naming($uploadFilename);
                            }

                            $do_upload = move_uploaded_file(
                                $temp_name,
                                $this->uploadDir .  $uploadFilename 
                            );

                            $callback($uploadFilename);
                        }
                    } else {
                        trigger_error("$uploadFilename is not a valid file upload. File uploading cancelled");
                        return false;
                    }
                }

                return true;
            } else {

                $temp_name = $this->uploadedFiles['tmp_name'];
                $uploadFilename = $this->uploadedFiles['name'];
                $uploadMime = $this->uploadedFiles['type'];
                if (is_uploaded_file($temp_name)) {
                    if ($this->allowedExt and $this->isAllowedExt($uploadMime)) {

                        if (is_callable($naming)) {
                            $uploadFilename = $naming($uploadFilename);
                        }

                        $do_upload = move_uploaded_file(
                            $temp_name,
                            $this->uploadDir . $uploadFilename
                        );
                        $callback($uploadFilename);
                        return $do_upload;
                    } else {
                        trigger_error("File extention not allowed");
                        return false;
                    }
                }
            }
        } else {
            trigger_error("/upload_dir not specified. File uploading cancelled");
        }
    }



    private function isAllowedExt($mime)
    {


        list(, $ext) = explode("/", $mime);

        if (!$ext) return false;

        var_dump($ext);

        return in_array($ext, $this->allowedExt);
    }


    public function allowOnly(array $mimeType)
    {

        $this->allowedExt = $mimeType;

        return $this;
    }



    public function setUploadDir($dir)
    {

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] .
            Router::getConfig('base_path') .
            Router::getConfig('static',) .
            $dir;

        if (is_dir($uploadDir)) {
            $this->uploadDir = $uploadDir . "/";
            return $this;
        } else {
            trigger_error("/$dir is not a valid upload directory");
            return false;
        }
    }
}
