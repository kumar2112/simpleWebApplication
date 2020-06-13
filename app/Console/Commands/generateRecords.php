<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
Use App\Routers;
class generateRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routers:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate {n} numbers of records.';

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
    public function handle()
    {
        $n = $this->ask('Please enter the number of records you want to add');
        if(!is_numeric($n)){
            $this->error('Please enter a number!');
        }
        $bar = $this->output->createProgressBar($n);
        $bar->start();

        $sapIdPrefix="SAP00";
        for($i=0;$i<$n;$i++){
            $max_id=Routers::max('id');
            if(!empty($max_id)){
               $sap_id=$sapIdPrefix.$max_id;
            }else{
               $sap_id=$sapIdPrefix."1";
            }
            $router=new Routers;
            $router->sap_id=$sap_id;
            $router->internet_host_name='example_'.$i.'.com';
            $router->client_ip_address="192.11.2.".$max_id;
            $router->mac_address="233:335:33:".$i;
            $router->save();
            echo "Record with sapid ".$sap_id. " inserted .\n";
        }
        $bar->finish();

        $this->info("\n".$n .' Records added successfully.');
    }
}
