<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Admin - Pedido {{$pedido->id}}</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

  <!-- DataTables -->
  <link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('admin/css/dataTable.css') }}" media="screen" title="no title" charset="utf-8">

</head>

<body>
  <div id="wrapper">
    @include('layouts.menu')
    <div id="page-wrapper" class="gray-bg">
      @include('layouts.top')
      <div class="wrapper wrapper-content">
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <div class="">
              <h1 class="">Pedido {{ $pedido->id }} @if($pedido->status == "0") <span class="pull-right" style="font-weight: bold; font-size: 20px;">Venda Cancelada em: {{ Carbon\Carbon::parse($pedido->data_cancel)->format('d/m/Y H:i:s') }}</span> @else <span style="color:green; font-weight: bold; font-size: 20px;" class="pull-right">Venda Concluida</span> @endif</h1>
            </div>
          </div>
          <div class="ibox-content" >
            <!-- Tabela dos Franqueados e Fda  -->
            <div class="row">
              <div class="col-lg-12">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <!-- <th>#</th> -->
                        <th>Fda</th>
                        <th>Franqueado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>@if($pedido->fdaid != 0 && \App\Models\Fda::where('id',$pedido->fdaid)->first() != null) ({{ \App\Models\Fda::where('id',$pedido->fdaid)->first()->fdaid }})  - {{ \App\Models\Fda::where('id',$pedido->fdaid)->first()->nome_razao }} @else Não Cadastrado @endif</td>
                        <td>@if($pedido->franqueadoid != 0 && \App\Models\Franqueado::where('id',$pedido->franqueadoid)->first() != null) ({{ \App\Models\Franqueado::where('id',$pedido->franqueadoid)->first()->franqueadoid }}) - {{ \App\Models\Franqueado::where('id',$pedido->franqueadoid)->first()->nome_razao }} @else Não Cadastrado @endif</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- /Tabela dos Franqueados e Fda -->
            <div class="row">
              <div class="col-lg-12">
                <!-- <h4>Informações do Cliente</h4> -->
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Id Acqio</th>
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Valor da Compra</th>
                        <th>Aprovado em</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          @if($pedido->versao_sis == "VA")
                            <a target="_blank" href="https://www.foccus.co/acqio/process/check-payment/id/{{ $pedido->id_acqio }}"><b>#{{ $pedido->id_acqio }}</b></a>
                          @elseif($pedido->versao_sis == "VN")
                            <a target="_blank" href="https://credenciamento.acqiopayments.com.br/#!/andamento/checagem-pagamento/{{ $pedido->id_acqio }}"><b>#{{ $pedido->id_acqio }}</b></a>
                          @else
                            <a href="#"><b>KissFlow</b></a>
                          @endif
                        </td>
                        <td>
                            @if($cliente->cpf == null)
                            {{ strtoupper($cliente->razao) }}
                            @else
                            {{ strtoupper($cliente->nome) }}
                            @endif
                        </td>
                        <td>
                          @if($cliente->cpf == null)
                            {{ $cliente->cnpj }}
                          @else
                            {{ $cliente->cpf }}
                          @endif
                        </td>
                        <td>
                          <?php $total = App\Models\PedidosItens::select(DB::raw('SUM(quantidade*preco_unit) as total'))->where('pedido_id','=',$pedido->id)->first()->total; ?>
                          R$ {{ number_format($total,2) }}
                        </td>
                        <td>
                          {{ date('d/m/Y H:i:s', strtotime(App\Models\Pedidos::where('id',$pedido->id)->value('created_at'))) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- Pagamentos -->
              <div class="col-lg-12">
                <br><br>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Identificador do Pagamento</th>
                        <th>Valor R$</th>
                        <th>Status</th>
                        <th>Data do Pagamento</th>
                        <th>Origem do Pagamento</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Valor Total Pago da compra -->
                      <?php $valorTotalPago = 0; ?>
                      @if(($pagamentos) != '')
                        @foreach($pagamentos as $p)
                        <tr>
                          @if($p->numero != null)
                          <!-- Valor total pago boleto -->
                          <?php $valorTotalPago += (float)$p->valor; ?>
                          <td>{{ $p->numero }}</td>
                          <td>R$ {{ number_format($p->valor,2) }}</td>
                          <td>{{ $p->situacao }}</td>
                          <td>{{ date('d/m/Y', strtotime($p->data)) }}</td>
                          <td>@if(strlen($p->numero) >= 11 && strlen($p->numero) <= 13) Bradesco @else Banco do Brasil @endif</td>
                          @else
                          <!-- Valor total pago Cartao -->
                          <?php $valorTotalPago += (float)$p->cod_vt; ?>
                          <td>{{ $p->codigo }}</td>
                          <td>R$ {{ number_format($p->cod_vt,2) }}</td>
                          <td>
                            @if($p->cod_status == 'Concluida')
                            <b style="color: green">{{ $p->cod_status }}</b>
                            @else
                            <b style="color: red">{{ $p->cod_status }}</b>
                            @endif
                          </td>
                          <td>{{ date('d/m/Y', strtotime($p->cod_data)) }}</td>
                          <td>{{ $p->cod_origem }}</td>
                          @endif
                        </tr>
                        @endforeach
                      @endif
                    </tbody>
                    <tfoot>
                      <tr>
                        <td>Total Pagamentos</td>
                        <td>R$ {{ number_format($valorTotalPago,2) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

              </div>
            </div>
            <!-- End Row -->

            <!-- Start Row -->
            <div class="row">
              <br><br>
              <div class="col-lg-12">
                <p>
                  @if($pedido->status == "1")
                    Observacao:
                  @else
                    Motivo do Cancelamento:
                  @endif
                </p>
                <textarea rows="5" col="20" class="form-control" readonly >@if($pedido->status == "1") {{$pedido->observacao}} @else {{$pedido->motivo}} @endif </textarea>
              </div>
            </div>
            <!-- End Row -->

            <!-- Row Itens -->
            <div class="row">
              <div class="col-lg-12">
                <hr>
                <h1 class="">Produtos</h1>
                <!-- Start Row -->
                <?php $quantItens = App\Models\PedidosItens::select('pedidos_itens.*')->where('pedido_id','=',$pedido->id)->get(); ?>
                <div class="row">
                  @foreach($quantItens as $i)
                  <div class="col-lg-3">
                    <h5 class="text-center">
                      Quantidade: {{ $i->quantidade }} - R$ {{ number_format($i->quantidade*$i->preco_unit,2) }}
                    </h5>
                    <a href="#" class="thumbnail">
                      <img src="{{ asset('img/acqio.png') }}" alt="...">
                    </a>
                    <div class="caption text-center">
                      <h3>
                        {{ App\Models\Produto::where('id',$i->produto_id)->value('descricao') }}

                      </h3>
                      <p></p>
                    </div>
                  </div>
                  @endforeach
                </div><!-- End Row -->
              </div>
            </div>
            <!-- Finish Row Itens  -->
          </div>
        </div>
      </div>
      @include('layouts.footer')
    </div>
    @include('layouts.right-side-bar')
  </div>

  <!-- Mainly scripts -->
  <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
  <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

  <!-- Custom and plugin javascript -->
  <script src="{{ asset('admin/js/inspinia.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

  <!-- jQuery UI -->
  <script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

  <!-- DataTable -->
  <script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>


</body>
</html>
