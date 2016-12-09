<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Queue::after(function (JobProcessed $event) {
        if(\DB::table('jobs')->count() == 0){
          \Vinkla\Pusher\Facades\Pusher::trigger('my-channel', 'generate_pdfs',array('message' => 'Todos os pdfs foram gerados com sucesso!!!','class'=>'success'));
        }
        // dd($event);
          // $event->connectionName
          // $event->job
          // $event->job->payload()
      });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
