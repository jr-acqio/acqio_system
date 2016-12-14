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

    private $month;
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
        $this->month = mes_extenso(\Carbon\Carbon::now()->subMonth()->format('m'));
        // dd($this);
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
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fda'=>$this->comissoes,'fda'=>$this->cliente]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->fdaid).'_'.$this->month.'.pdf');
        //Após salvar no storage devemos criar as ordens de pagamento e setar as comissoes que estão relacionadas a um pagamento.

        $order_payment = new \App\Models\OrdemPagamento;
        $order_payment->relatorio_pdf = '/app/'.$this->folder.'/'.strtoupper($this->cliente->fdaid).'_'.$this->month.'.pdf';
        $order_payment->mes_ref = \Carbon\Carbon::now()->subMonth()->format('m');
        $order_payment->valor = $this->comissoes->sum('tx_instalacao');
        $order_payment->save();
        foreach ($this->comissoes as $key => $value) {
          // dd($this->comissoes[$key]);
          DB::select('UPDATE comissoes SET order_id = '.$order_payment->id.' WHERE comissoes.id = '.$this->comissoes[$key]->comissaoid);
        }

      }
      else if($this->type == 2){
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fr'=>$this->comissoes,'franqueado'=>$this->cliente]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->franqueadoid).'_'.$this->month.'.pdf');
        //Após salvar no storage devemos criar as ordens de pagamento e setar as comissões e royalties que estão relacionadas a um pagamento.

        $order_payment = new \App\Models\OrdemPagamento;
        $order_payment->relatorio_pdf = '/app/'.$this->folder.'/'.strtoupper($this->cliente->franqueadoid).'_'.$this->month.'.pdf';
        $order_payment->mes_ref = \Carbon\Carbon::now()->subMonth()->format('m');
        $order_payment->valor = $this->comissoes->sum('tx_venda');
        $order_payment->save();
        foreach ($this->comissoes as $key => $value) {
          DB::select('UPDATE comissoes SET order_id = '.$order_payment->id.' WHERE comissoes.id = '.$this->comissoes[$key]->comissaoid);
        }
      }
    }
}
