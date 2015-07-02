<?php namespace App\Http\Controllers;

/**
 * Class OneTimeFileBaseController
 *
 * Base class for controllers.
 */
class OneTimeFileBaseController extends Controller
{
    protected $model;

    /**
     * Detect language by accept language.
     *
     * @return string Detected language.
     */
    private function detectLanguage()
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

        switch (mb_strtolower($lang)) {
            case 'ru':
                return 'russian';

            default:
                return 'english';
        }
    }
}