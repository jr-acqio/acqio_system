<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Relatório de Comissão</title>

  <link href="admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

  <link href="admin/css/animate.css" rel="stylesheet">
  <link href="admin/css/style.css" rel="stylesheet">

</head>

<body class="white-bg">
  <div class="wrapper wrapper-content p-xl">
    <div class="ibox-content p-xl">
      <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
          <img src="img/logo.png" alt="" />
        </div>

        <div class="col-sm-6">
          <h1>@if(isset($fda)) FDA: {{$fda->nome_razao}} @else FRANQUEADO: {{$franqueado->nome_razao}} @endif</h1>
        </div>
        <div class="col-sm-6 text-right">
          <p>
            <span><strong>Data inicial:</strong> {{ date('d/m/Y', strtotime($periodo['data_inicial'])) }}</span><br/>
            <span><strong>Data final:</strong> {{ date('d/m/Y', strtotime($periodo['data_final'])) }}</span>
          </p>
        </div>
      </div>

      <div class="table-responsive m-t">
        <table class="table invoice-table">
            <?php $sum = 0; ?>
            @if(isset($comissoes_fda))
            <thead>
              <tr>
                <th>Data Cadastro</th>
                <th>Data Aprovação</th>
                <th>Franqueado</th>
                <th>Cliente</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Valor R$</th>
              </tr>
            </thead>
            <tbody>
              @foreach($comissoes_fda as $c)
              <tr>
                <td>{{ date('d/m/Y', strtotime($c->data_cadastro)) }}</td>
                <td>{{ date('d/m/Y',strtotime($c->data_aprovacao)) }}</td>
                <td>{{ strtoupper($c->franqueadoid) }}</td>
                <td>{{ strtoupper($c->nome_cliente) }}</td>
                <td>
                  {{ \App\Models\Comissoes::find($c->comissaoid)->produtos->implode('descricao',', ') }}
                </td>
                <td class="text-center">{{ $c->totalProdutos }}</td>
                <td>R$ {{ number_format($c->totalInstalacao,2) }}</td>
              </tr>

              <?php $sum += $c->totalInstalacao; ?>
              @endforeach
            @else
              <?php $sum = 0; ?>
              <thead>
                <tr>
                  <th>Data Cadastro</th>
                  <th>Data Aprovação</th>
                  <!-- <th>Franqueado</th> -->
                  <th>Cliente</th>
                  <th>Produto</th>
                  <th>Quantidade</th>
                  <th>Valor R$</th>
                </tr>
              </thead>
              <tbody>
              @foreach($comissoes_fr as $c)
              <tr>
                <td>{{ date('d/m/Y', strtotime($c->data_cadastro)) }}</td>
                <td>{{ date('d/m/Y',strtotime($c->data_aprovacao)) }}</td>
                <!-- <td>{{ strtoupper(DB::table('franqueados')->where('id',$c->franqueadoid)->value('nome_razao')) }}</td> -->
                <td>{{ strtoupper($c->nome_cliente) }}</td>
                <td>
                  {{ \App\Models\Comissoes::find($c->comissaoid)->produtos->implode('descricao',', ') }}
                </td>
                <td class="text-center">{{ \App\Models\Comissoes::find($c->comissaoid)->produtos->count() }}</td>
                <td>R$ {{ number_format($c->totalVenda,2) }}</td>
              </tr>
              <?php $sum += $c->totalVenda; ?>
              @endforeach

            @endif
          </tbody>
        </table>
      </div><!-- /table-responsive -->

      <div class="col-lg-6">
        @if(isset($fda))
          Total de Vendas: {{ $comissoes_fda->count() }}<br>
          Total de POS: {{ $comissoes_fda->sum('totalProdutos') }}
        @else
          Total de Vendas: {{ $comissoes_fr->count() }}<br>
          Total de POS: {{ $comissoes_fr->sum('totalProdutos') }}
        @endif
      </div>
      <div class="col-lg-6">
        <table class="table invoice-total text-right">
          <tbody>
            <tr>
              <td><strong>Total de Comissões:</strong></td>
              <td>R$ {{ number_format($sum,2,',', '.') }}</td>
            </tr>
            @if(isset($franqueado))
              @if(!is_null($franqueado->totalRoyalties))
                <tr>
                  <td><strong>Royalties vencidos:</strong></td>
                  <td>- R$ {{ number_format($franqueado->totalRoyalties ,2,',', '.') }}</td>
                </tr>
                <tr>
                  <td><strong>Cheques devolvidos:</strong></td>
                  <td>- R$ {{ number_format($franqueado->totalChequesDevolvidos ,2,',', '.') }}</td>
                </tr>
                <tr>
                  <td><strong>Valor final:</strong></td>
                  <td>R$ {{ number_format($franqueado->valorFinal,2,',', '.') }}</td>
                </tr>
              @else
                <tr>
                  <td><strong>Valor final:</strong></td>
                  <td>R$ {{ number_format($franqueado->valorTotal,2,',', '.') }}</td>
                </tr>
              @endif
            @else
              <tr>
                <td><strong>Valor final:</strong></td>
                <td>R$ {{ number_format($fda->valorTotal,2,',', '.') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

    </div>

  </div>

  <!-- Mainly scripts -->
  <script src="admin/js/jquery-2.1.1.js"></script>
  <script src="admin/js/bootstrap.min.js"></script>
  <script src="admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>

  <!-- Custom and plugin javascript -->
  <script src="admin/js/inspinia.js"></script>

  <script type="text/javascript">
  // window.print();
  </script>

</body>

</html>
