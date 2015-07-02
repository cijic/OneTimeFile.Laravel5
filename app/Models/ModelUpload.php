<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

/**
 * Class for working with DB for uploading process.
 */
class ModelUpload extends Model
{
    protected $data;

    /**
     * Hash specified string.
     * @param  string $str - String to be hashed.
     * @return string - Hashed string.
     */
    protected function hash($str)
    {
        return hash('sha256', hash('sha256', $str));
    }

    /**
     * Generate unique URL for access to uploaded file.
     * @param string $filePath - Abs file path without filename.
     * @param string $fileName - Filename.
     * @return string - Unique URL for access to uploaded file.
     * @throws \Exception
     */
    protected function generateUrl($filePath, $fileName)
    {
        if (empty($filePath) || empty($fileName)) {
            throw new Exception('No needful data.', 1);
        }

        return $this->hash($filePath) . '/' . $this->hash($fileName);
    }

    /**
     * Generate short URL for file downloading.
     * @param  string $url - URL which must be unuque and shorted.
     * @return string - Shorted URL.
     */
    protected function generateShortUrl($url)
    {
        return $this->hash($url);
    }

    /**
     * Drop limit for specified IP.
     * @param  string $ip - IP address
     */
    protected function dropLimit($ip)
    {
        $dropSQL =
            'DELETE
             FROM ban
             WHERE ip = ?';
        DB::delete($dropSQL, [$ip]);
    }

    /**
     * Check if banlist contains specified IP.
     * @param  string $ip - IP address.
     * @return boolean - True - if contains. False - if not.
     */
    public function banlistContains($ip)
    {
        $checkSQL =
            'SELECT ip, time
             FROM ban
             WHERE ip = ?';
        $data = DB::select($checkSQL, [$ip]);

        if (!count($data) or empty($data[0]->ip)) {
            return false;
        }

        $data = $data[0];
        $timestamp = (int)($data->time);
        $timePassed = time() - $timestamp;
        $timeLimit = 60 * 60;    // Time in seconds for 1 hour.

        if ($timePassed > $timeLimit) {
            $this->dropLimit($ip);
            return false;
        }

        return true;
    }

    /**
     * Add specified IP to ban list.
     * @param string $ip - IP address.
     */
    protected function addToBanlist($ip)
    {
        $addToBanlistSQL =
            "INSERT INTO ban (ip, time)
             VALUES (?, ?)";
        DB::insert($addToBanlistSQL, [$ip, time()]);
    }

    /**
     * Save specified data to DB.
     * @param string $uniqueUrl - Long unique URL for downloading file.
     * @param string $shortUniqueUrl - Short unique URL for downloading file.
     * @param string $password Password for accessing it.
     */
    protected function saveFileData($uniqueUrl, $shortUniqueUrl, $password = '')
    {
        $saveSQL =
            "INSERT INTO files (url, short_url, local_path, filename, time, password)
             VALUES
              (?,
               ?,
               ?,
               ?,
               ?,
               ?)";
        $params = [];
        $params[] = $uniqueUrl;
        $params[] = $shortUniqueUrl;
        $params[] = $this->data['full_path'];
        $params[] = $this->data['client_name'];
        $params[] = time();
        $params[] = $password; // TODO: Add support for premium user set password.
        DB::insert($saveSQL, $params);
    }

    /**
     * Get download path for uploaded file.
     * @return string - Get full path for website.
     */
    public function getPath()
    {
        $filePath = (empty($this->data['file_path'])) ? '' : $this->data['file_path'];
        $fileName = (empty($this->data['file_name'])) ? '' : $this->data['file_name'];
        $result = '';

        try {
            $uniqueUrl = $this->generateUrl($filePath, $fileName);
            $shortUniqueUrl = $this->generateShortUrl($uniqueUrl);
            $result = $uniqueUrl . ' ' . $shortUniqueUrl;
            $this->saveFileData($uniqueUrl, $shortUniqueUrl);
            $this->addToBanlist($_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            echo $e->getMessage();
            return '';
        }

        return $result;
    }

    /**
     * Save data of uploaded file.
     * @param array $data - Array with needful data of uploaded file.
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}