<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use PDF;
class GeradorPdfComissoes extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $folder;
    public $comissoes;
    public $cliente;
    public $type; //Type = 1 Fda, Type = 2 Franqueado
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folder,$comissoes,$cliente,$type)
    {
        $this->folder = $folder;
        $this->comissoes = $comissoes;
        $this->cliente = $cliente;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // Criar o pdf
      if($this->type == 1){
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fda'=>$this->comissoes,'fda'=>\App\Models\Fda::where('fdaid',$this->cliente->fdaid)->first()]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->fda).'/'.strtoupper($this->cliente->fdaid).'_'.\Carbon\Carbon::now()->format('d-m-Y').'.pdf');
      }
      else if($this->type == 2){
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fr'=>$this->comissoes,'franqueado'=>\App\Models\Franqueado::where('franqueadoid',$this->cliente->franqueadoid)->first()]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->franqueadoid).'_'.\Carbon\Carbon::now()->format('d-m-Y').'.pdf');
      }
    }
}
