<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Http\Controllers\ControllerRoute;
use App\Models\ModelRoute;

class RemoveLimitated extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'remove_limitated';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove limitated records.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->cliRemoveLimitated();
	}

    /**
     * Remove limitated records which are old, and corresponding row in DB.
     */
    protected function cliRemoveLimitated()
    {
        $model = new ModelRoute();
        $this->info('Started CLI mode...');
        $timeLimit = 60 * 60 * 12; // Timelimit = 12 hour
        $result = $model->getFiles();
        $this->info("Total records:" . count($result));

        for ($i = 0; $i < count($result); $i++) {
            $data = $result[$i];
            $timeUpload = $data->time;
            $timePassed = time() - (int)($timeUpload);

            if ($timePassed > $timeLimit) {
                $model->setLocalPath($data->local_path);
                $model->delete();
                $this->info('Deleted: ' . $data->local_path);
            }
        }

        $this->info('Finishing CLI mode...');
    }
}
