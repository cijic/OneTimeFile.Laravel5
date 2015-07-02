<?php namespace App\Http\Controllers;

use App\Models\ModelRoute;

/**
 * Class for routing downloadable files.
 */
class ControllerRoute extends OneTimeFileBaseController
{
    public function __construct()
    {
        $this->model = new ModelRoute();
    }

    /**
     * Method for force upload file to user.
     * @param  string $localPath Local path of file.
     * @param  string $filename Filename, which will be saved on user side.
     */
    protected function forceDownload($localPath, $filename)
    {
        if (file_exists($localPath)) {
            header('Content-Description: localPath Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($filename));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($localPath));
            readfile($localPath);
        }
    }

    /**
     * Upload requested file if was found.
     * @param array $data - Needful data.
     */
    protected function uploadToUser($data)
    {
        if (empty($data)) {
            abort(404);
        }

        $filename = $data->filename;
        $localPath = $data->local_path;
        $localPath = realpath($localPath);

        if (file_exists($localPath)) {
            $this->forceDownload($localPath, $filename);
            $this->model->delete();
        }
    }

    /**
     * Routing downloadable files.
     * @param  string $url - URL for download.
     */
    public function route($url)
    {
        $data = $this->model->getData($url);
        $this->uploadToUser($data);
    }
}