<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\RoyaltiesOrdemPagamento;
use App\Models\ComissaoOrdemPagamento;
use PDF;
class GeradorPdfComissoes extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $folder;
    public $comissoes;
    public $cliente;
    public $type; //Type = 1 Fda, Type = 2 Franqueado
    private $month;
    public $periodo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folder,$params,$comissoes,$cliente,$type)
    {
        $this->folder = $folder;
        $this->comissoes = $comissoes;
        $this->cliente = $cliente;
        $this->type = $type;
        $this->month = mes_extenso(\Carbon\Carbon::now()->subMonth()->format('m'));
        $this->periodo = $params;
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
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fda'=>$this->comissoes,'fda'=>$this->cliente,'periodo'=>$this->periodo]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->fdaid).'_'.$this->month.'.pdf');
        //Após salvar no storage devemos criar as ordens de pagamento e setar as comissoes que estão relacionadas a um pagamento.

        $order_payment = new \App\Models\OrdemPagamento;
        $order_payment->relatorio_pdf = 'app/'.$this->folder.'/'.strtoupper($this->cliente->fdaid).'_'.$this->month.'.pdf';
        $order_payment->mes_ref = \Carbon\Carbon::now()->subMonth()->format('m');
        $order_payment->valor = $this->comissoes->sum('totalInstalacao');
        $order_payment->fdaid = $this->cliente->id;
        $order_payment->save();
        // dd($order_payment);
        foreach ($this->comissoes as $key => $value) {
          $comissao_order_payment = new ComissaoOrdemPagamento;
          $comissao_order_payment->idcomissao = $this->comissoes[$key]->comissaoid;
          $comissao_order_payment->idordempagamento = $order_payment->id;
          $comissao_order_payment->save();
        }

      }
      else if($this->type == 2){
        $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fr'=>$this->comissoes,'franqueado'=>$this->cliente,'periodo'=>$this->periodo]);
        $pdf->save(storage_path().'/app/'.$this->folder.'/'.strtoupper($this->cliente->franqueadoid).'_'.$this->month.'.pdf');
        //Após salvar no storage devemos criar as ordens de pagamento e setar as comissões e royalties que estão relacionadas a um pagamento.

        $order_payment = new \App\Models\OrdemPagamento;
        $order_payment->relatorio_pdf = 'app/'.$this->folder.'/'.strtoupper($this->cliente->franqueadoid).'_'.$this->month.'.pdf';
        $order_payment->mes_ref = \Carbon\Carbon::now()->subMonth()->format('m');
        $order_payment->valor = $this->cliente->valorFinal;
        $order_payment->franqueadoid = $this->cliente->id;
        $order_payment->save();
        foreach ($this->comissoes as $key => $value) {
          $comissao_order_payment = new ComissaoOrdemPagamento;
          $comissao_order_payment->idcomissao = $this->comissoes[$key]->comissaoid;
          $comissao_order_payment->idordempagamento = $order_payment->id;
          $comissao_order_payment->save(); 
        }

        $hasRoyalties = \App\Models\Franqueado::where('id',$this->cliente->id)->first()->hasRoyalties()->where('descontado','!=','s')->get();
        foreach ($hasRoyalties as $key => $value) {
          $value->descontado = 's';
          $value->save();
          $royalties_order_payment = new RoyaltiesOrdemPagamento;
          // dd($royalties_order_payment,$hasRoyalties);
          $royalties_order_payment->idroyalties = $value->id;
          $royalties_order_payment->idordempagamento = $order_payment->id;
          $royalties_order_payment->save();
        }
      }
    }
}
