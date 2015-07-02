<?php

use App\Models\Seeding\ModelBan;
use App\Models\Seeding\ModelFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call('FilesSeeder');
        $this->call('BansSeeder');
    }
}

class FilesSeeder extends Seeder
{
    public function run()
    {
        DB::table('files')->delete();
        $this->creation();
    }

    /**
     * Method for fill table with (specified) data.
     */
    private function creation()
    {
        ModelFiles::create([
            'url' => hash('sha256', date('YMHs')) . '/' . hash('sha256', date('sss')),
            'short_url' => hash('sha256', date('DM')),
            'local_path' => 'E:\Музыка\!\\' . hash('sha256', date('s')) . ' .mp3',
            'filename' => 'Default.filename.ext',
            'time' => DB::raw('CURRENT_TIMESTAMP')
        ]);

        ModelFiles::create([
            'url' => hash('md5', date('YMHs')) . '/' . hash('sha256', date('sss')),
            'local_path' => 'E:\Музыка\!\\' . hash('md5', date('s')) . ' .mp3',
            'short_url' => hash('md5', date('DM')),
            'filename' => 'Default.filename.ext',
            'time' => DB::raw('CURRENT_TIMESTAMP'),
            'password' => hash('md5', date('Y'))
        ]);
    }
}

class BansSeeder extends Seeder
{
    public function run()
    {
        DB::table('ban')->delete();
        $this->creation();
    }

    /**
     * Inserting data in the "ban" table.
     */
    private function creation()
    {
        ModelBan::create($this->generateFakeData());
        ModelBan::create($this->generateFakeData());
        ModelBan::create($this->generateFakeData());
    }

    /**
     * Generate fake data for fill it in "ban" tabel.
     * @return array - Array with fake data.
     */
    private function generateFakeData()
    {
        $result = [];
        $result['ip'] = (string)(rand(0, 255)) . '.' .  (string)(rand(0, 255)) . '.' . (string)(rand(0, 255)) . '.' . (string)(rand(0,
                255));
        $result['time'] = (string)time();
        return $result;
    }
}
