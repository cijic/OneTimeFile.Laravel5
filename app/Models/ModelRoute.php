<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model for working with requested files.
 */
class ModelRoute extends Model
{
    protected $localPath;

    /**
     * Set local path of current file.
     * @param string $localPath - Local abs path to file.
     */
    public function setLocalPath($localPath)
    {
        $this->localPath = $localPath;
    }

    /**
     * Get abs path for requested file.
     * @param  string $url - Requested URL.
     * @return array - Array of needful data.
     */
    public function getData($url)
    {
        $getAbsPath =
            'SELECT filename, local_path
             FROM files
             WHERE short_url = ? OR url = ?';
        $params = [];
        $params[] = $url;
        $params[] = $url;
        $data = DB::select($getAbsPath, $params);

        if (!count($data)) {
            return null;
        }

        $data = $data[0];

        if (empty($data->filename) ||
            empty($data->local_path)) {
            return null;
        }

        $this->localPath = $data->local_path;
        return $data;
    }

    /**
     * Delete data of requested file from DB.
     */
    public function deleteFromDB()
    {
        $deleteSQL =
            "DELETE
             FROM files
             WHERE local_path = ?";
        DB::delete($deleteSQL, [$this->localPath]);
    }

    /**
     * Delete local file.
     */
    public function deleteLocal()
    {
        unlink($this->localPath);
    }

    /**
     * Delete data in DB and corresponding file.
     */
    public function delete()
    {
        $this->deleteFromDB();
        $this->deleteLocal();
    }

    /**
     * Get files info from table "files".
     * @return array: Array with requested info.
     */
    public function getFiles()
    {
        $selectSQL =
            'SELECT *
             FROM files';
        $data = DB::select($selectSQL);
        return $data;
    }
}