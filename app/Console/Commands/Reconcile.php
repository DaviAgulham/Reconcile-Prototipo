<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Company;

class Reconcile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera la conciliacion del ultimo mes';

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
     * @return int
     */
    public function handle()
    {
        
        $month = (string)(now()->month-1);
        $company_id = 1;
        $company = Company::where('unique_id', $company_id)->first();
        $company_name = strtoupper($company->name);

        $archive = "RECONCILE_{$month}_{$company_name}.simp2";
        $this->output->info("Conciliando mes $month");
              
        $payments = Payment::where('notified_at_month', $month)->get();

        $fp = fopen($archive, "w");

        foreach($payments as $payment){
            $a = $this->formatLine($payment) . PHP_EOL;
            fwrite($fp, $a);
        }
        
        fclose($fp);
        return Command::SUCCESS;
    }

    private function formatLine(Payment $payment): string{
        $line = $this->formatString($payment->barcode_sequence, 8, true);
        $line .= $this->formatString($payment->unique_reference, 50, false);  
        //$line .= $this->formatString("Code", 50, false);
        $line .= $this->formatString($payment->amount, 10, true);  
        $line .= $this->formatString($payment->confirmed_amount, 10, true);  
        $line .= $this->formatString($payment->status, 25, false);  
        $line .= $this->formatDate($payment->created_at);  
        $line .= $this->formatDate($payment->notified_at);  
        $line .= $this->formatDate($payment->confirmed_at);  
        $line .= $this->formatDate($payment->rollback_confirmed_at);  
        
        return $line;
    }

    private function formatDate($date): string{
        if (!$date) return '00000000000000';
        
        if (is_string($date)) return $date;
        if (is_array($date)) return date('YmdHis', $date["due_date"]["\$date"]["\$numberLong"] / 1000);
        return $date->toDateTime()->format('YmdHis');

    }

    private function formatString($string, $size, bool $number): string{
          
        if ($number){
            return str_pad(strval($string),  $size, '0', STR_PAD_LEFT);
        }
        return str_pad(strval($string),  $size, ' ', STR_PAD_RIGHT);
    }
}
