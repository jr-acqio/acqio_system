<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Models\Franqueado;
use App\Models\Fda;
class SendEmailsComissions extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    public $type;
    public $dados;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type,$dados)
    {
        $this->type = $type;
        $this->dados = $dados;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($this->dados);
        if($this->type == 1){
          Mail::send('admin.mails.emails-fda', ['data'=>$this->dados], function($message){
            // Set the receiver and subject of the mail.
            $fda = Fda::where('id',$this->dados->id)->first();
            // dd($franqueado);
            // $message->to('stefano.andrei@esfera5.com.br');
            // $message->cc('joselito.junior@esfera5.com.br');
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            $message->to($fda->email);
            $message->bcc('stefano.andrei@esfera5.com.br');
            $message->bcc('leandro.xavier@esfera5.com.br');
            $message->bcc('joselito.junior@esfera5.com.br');

            $message->subject('Relatório de Comissões - Novembro');
            // Set the sender
            // dd($message);
            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/fdas/December_2016/'.strtoupper($fda->fdaid).'_09-12-2016.pdf');
          });
        }
        else if($this->type == 2){
          Mail::send('admin.mails.emails-franqueado', ['data'=>$this->dados], function($message){
            // Set the receiver and subject of the mail.
            $franqueado = Franqueado::where('id',$this->dados->id)->first();
            // dd($franqueado);
            // $message->to('stefano.andrei@esfera5.com.br');
            // $message->cc('joselito.junior@esfera5.com.br');
            $message->to($franqueado->email);
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            // $message->cc('financeiro1@acqiofranchising.com.br');

            $message->bcc('stefano.andrei@esfera5.com.br');
            $message->bcc('leandro.xavier@esfera5.com.br');
            $message->bcc('joselito.junior@esfera5.com.br');
            $message->subject('Relatório de Comissões - Novembro');
            // Set the sender

            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/franqueados/December_2016/'.strtoupper($franqueado->franqueadoid).'_09-12-2016.pdf');
          });
        }

    }
}
