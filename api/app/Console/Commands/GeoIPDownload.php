<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class GeoIPDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geoip:download';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download the latest GeoIP database';
    public $url;
    public $file_name;
    public $database_name;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = env('GEOIPURL');
        $this->file_name = storage_path('app/GeoLite2-City.mmdb.gz');
        $this->database_name = storage_path('app/GeoLite2-City.mmdb');
    }
    public function deleteFiles()
    {
        if (file_exists($this->file_name)) {
            unlink($this->file_name);
            $this->line(PHP_EOL . 'Deleted gzip file.');
        }

    }
    public function downloadFile()
    {
        set_time_limit(0);
        //This is the file where we save the information
        $fp = fopen($this->file_name, 'w+');
        //Here is the file we are downloading, replace spaces with %20
        $ch = curl_init(str_replace(" ", "%20", $this->url));
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        // write curl response to file
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // get curl response
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
    public function unzipFile()
    {
        // Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        // Open our files (in binary mode)
        $file = gzopen($this->file_name, 'rb');
        $out_file = fopen($this->database_name, 'wb');
        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }
        // Files are done, close files
        fclose($out_file);
        gzclose($file);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            
            $this->downloadFile();
            $this->unzipFile();
            $this->comment(PHP_EOL . 'Success! We downloaded and extracted the new database! ' . $this->database_name . ' was created.' . PHP_EOL);
            $this->info("SECONDCHECK:".(time() + (3600 * 24 * 8))."\n");
            $this->deleteFiles();

        } catch (\Exception $e) {
            $this->error(PHP_EOL . $e->getMessage() . PHP_EOL);
        }
    }
}