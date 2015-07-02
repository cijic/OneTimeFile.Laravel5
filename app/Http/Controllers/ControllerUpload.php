<?php namespace App\Http\Controllers;

use App\Models\ModelUpload;
use Exception;
use Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ControllerUpload extends OneTimeFileBaseController
{
    public function __construct()
    {
        $this->model = new ModelUpload();
    }

    public function index()
    {
        return $this->printPage();
    }

    /**
     * Print page.
     */
    private function printPage()
    {
        $params = [];
        $params['title'] = 'Upload';
        $banListContains = $this->model->banlistContains($_SERVER['REMOTE_ADDR']);

        if ($banListContains) {
            $params['errorMessage'] = 'IP limits for uploading. Please, return after 1 hour.';
            return view('templates/error_view', $params);
        }

        return view('templates/upload', $params);
    }

    /**
     * Generate
     * @param string $ext : Extension
     * @return string: Hashed filename. Extension remain as is.
     */
    protected function generateUniqueName($ext)
    {
        return hash('md5', (string)time()) . '.' . $ext;
    }

    protected function checkRules($file, $config)
    {
        if ($file instanceof UploadedFile) {
            if ($file->getClientSize() > $config['max_size']) {
                return false;
            }

            return true;
        }

        throw new Exception('Not supported type.');
    }

    /**
     * Handler path specified operations.
     *
     * @return string File path.
     * @throws Exception
     */
    private function processPath()
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../../';
        $path .= '/uploads/' . date('Ymd') . '/';

        if (!file_exists($path) && !mkdir($path)) {
            throw new Exception('Error on creating new directory.');
        }

        return $path;
    }

    /**
     * Initialize upload settings.
     */
    private function initUploadSettings()
    {
        $config['allowed_types'] = '*';
        $config['upload_path'] = $this->processPath();
        $config['encrypt_name'] = true;
        $config['max_size'] = 1024 * 1024 * 300;
        $config['max_filename'] = 255;
        return $config;
    }

    /**
     * Upload file to the server.
     */
    public function uploadFile()
    {
        if ($this->model->banlistContains($_SERVER['REMOTE_ADDR'])) {
            return $this->jsonResponse(['result' => '']);
        }

        $userfileTag = 'userfile';
        $config = $this->initUploadSettings();

        if (Request::hasFile($userfileTag)) {
            $file = Request::file($userfileTag);

            if ($file->isValid() and
                $this->checkRules($file, $config)
            ) {
                $filename = null;

                if ($config['encrypt_name']) {
                    $filename = $this->generateUniqueName($file->getClientOriginalExtension());
                } else {
                    $filename = $file->getClientOriginalName();
                }

                $file->move($config['upload_path'], $filename);

                $data['file_path'] = $config['upload_path'];
                $data['file_name'] = $filename;
                $data['full_path'] = realpath($data['file_path'] . '/' . $data['file_name']);
                $data['client_name'] = $file->getClientOriginalName();

                $this->model->setData($data);
                return $this->jsonResponse(['path' => $this->model->getPath()]);
            }
        }

        return $this->jsonResponse(['result' => '']);
    }

    /**
     * Checking is user is banned.
     *
     * @return boolean: True - is banned. False - if not.
     */
    public function checkBanlistContains()
    {
        return $this->model->banlistContains($_SERVER['REMOTE_ADDR']) ?
            $this->jsonResponse(['result' => 'true']) :
            $this->jsonResponse(['result' => 'false']);
    }

    /**
     * Make JSON response with specified data.
     *
     * @param array $response : Response as array.
     */
    protected function jsonResponse(array $response)
    {
        return response()
            ->json($response);
    }
}