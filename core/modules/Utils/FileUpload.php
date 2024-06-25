

<?php


class FileUpload
{


    public $uploadDir = null;

    private array $allowedExt = [];

    public function __construct(
        public  $uploadedFiles,
        public $isMultiple
    ) {
    
    }


    public function upload()
    {


        if ($this->uploadDir) {
            if ($this->isMultiple) {
                for ($file = 0; $file < count($this->uploadedFiles['name']); ++$file) {

                    $temp_name = $this->uploadedFiles['tmp_name'][$file];
                    $uploadFilename = $this->uploadedFiles['name'][$file];
                    $uploadMime = $this->uploadedFiles['type'][$file];
                    if (is_uploaded_file($temp_name)) {

                        if ($this->allowedExt and !in_array($uploadMime, $this->allowedExt)) {
                            trigger_error("File extention not allowed");
                            return false;
                        } else {

                            echo "will upload " . $this->uploadDir . $uploadFilename;
                            $do_upload = move_uploaded_file(
                                $temp_name,
                                $this->uploadDir . $uploadFilename
                            );


                        }
                    } else {
                        trigger_error("$uploadFilename is not a valid file upload. File uploading cancelled");
                        return false;
                    }
                }

                return true;
            } else {

                $temp_name = $this->uploadedFiles['tep_name'];
                $uploadFilename = $this->uploadedFiles['name'];
                $uploadMime = $this->uploadedFiles['type'];
                if (is_uploaded_file($temp_name)) {
                    if ($this->allowedExt and !in_array($uploadMime, $this->allowedExt)) {
                        trigger_error("File extention not allowed");
                        return false;
                    } else {
                        $do_upload = move_uploaded_file(
                            $temp_name,
                            $this->uploadDir . $uploadFilename
                        );

                        return $do_upload;
                    }
                }
            }
        } else {
            trigger_error("/upload_dir not specified. File uploading cancelled");
        }
    }


    public function allow(array $mimeType)
    {

        $this->allowedExt = $mimeType;
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
