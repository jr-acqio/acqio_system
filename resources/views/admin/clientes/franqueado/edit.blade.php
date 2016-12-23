@extends('layouts.master')

@section('title')
Edit - {{$franqueado->franqueadoid}}
@endsection
@push('links')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

<link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

<!-- DataTables -->
<link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-12">
    <h2>Franqueados</h2>
    <ol class="breadcrumb">
      <li>
        <a href="/">Home</a>
      </li>
      <li>
        <a href="{{ route('admin.franqueado.index') }}">Franqueado</a>
      </li>
      <li class="active">
        <strong>Edit Franqueado</strong>
      </li>
    </ol>
  </div>
</div>
<br>
<div class="row">
  <div class="col-lg-12">
    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <h3>Editar Franqueado - {{ $franqueado->franqueadoid }}</h3>
      </div>
      <div class="ibox-content">
        {{ Form::model($franqueado, ['route' => ['admin.franqueado.update', $franqueado->id], 'method'=>'PUT']) }}
          <div class="row">
            <div class="form-group col-lg-4">
              {{ Form::label('t_pessoa', 'Tipo:') }}<br>
              <input type="radio" required name="t_pessoa" value="PF" @if(pessoa_fisica($franqueado->documento)) checked @endif>Pessoa Fisica
              <input type="radio" required name="t_pessoa" value="PJ" @if(!pessoa_fisica($franqueado->documento)) checked @endif>Pessoa Jurídica
            </div>
            <div class="form-group col-lg-4">
              <label for="">FDA:</label>
              <select class="form-control chosen-select" name="fdaid" required="">
                <option value="">ALTERAR FDA</option>
                @foreach($fdas as $f)
                <option value="{{$f->id}}" @if($f->id == $franqueado->fdaid) selected @endif>{{strtoupper($f->fdaid)}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-lg-2">
                {{ Form::label('created_at', 'Criado em:') }}
                <p>{{ date('d/m/Y H:i:s',strtotime($franqueado->created_at)) }}</p>
              </div>
              <div class="form-group col-lg-2">
                {{ Form::label('updated_at', 'Última atualização:') }}  
                <p>{{ date('d/m/Y H:i:s',strtotime($franqueado->updated_at)) }}</p>
              </div>
          </div>
          <div class="row">
              <div class="form-group col-lg-6">
                {{ Form::label('franqueadoid', 'franqueado ID') }}  
                {{ Form::text('franqueadoid', $franqueado->franqueadoid, ['class' => 'form-control', 'required'=> 'true']) }}
              </div>
              <div class="form-group col-lg-6">
                {{ Form::label('documento', 'Documento') }}  
                {{ Form::text('documento', $franqueado->documento, ['class' => 'form-control', 'required'=> 'true']) }}
              </div>
          </div>
          <div class="row">
            <div class="form-group col-lg-6">
              {{ Form::label('email', 'E-Mail Address') }}
              {{ Form::text('email', $franqueado->email, ['class' => 'form-control', 'required'=> 'true']) }}
            </div>
            <div class="form-group col-lg-6">
              {{ Form::label('nome_razao', 'Nome ou Razão Social') }}
              {{ Form::text('nome_razao', $franqueado->nome_razao, ['class' => 'form-control', 'required'=> 'true']) }}
            </div>
          </div>
          <div class="row">
            <div class="form-group col-lg-4">
              {{ Form::label('endereco', 'Endereço') }}
              {{ Form::text('endereco', $franqueado->endereco, ['class' => 'form-control']) }}
            </div>
            <div class="form-group col-lg-2">
              {{ Form::label('cep', 'CEP') }}
              {{ Form::text('cep', $franqueado->cep, ['class' => 'form-control']) }}
            </div>
            <div class="form-group col-lg-4">
              {{ Form::label('cidade', 'Cidade') }}
              {{ Form::text('cidade', $franqueado->cidade, ['class' => 'form-control', 'required'=> 'true']) }}
            </div>
            <div class="form-group col-lg-2">
              {{ Form::label('uf', 'UF') }}
              {{ Form::text('uf', $franqueado->uf, ['class' => 'form-control', 'required'=> 'true']) }}
            </div>
          </div>


          <div class="row">
            <div class="form-group col-lg-12">
              {{ Form::submit('Salvar Alterações', ['class'=>'btn btn-primary']) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<!-- Mainly scripts -->
<script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
<!-- <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script> -->
<script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('admin/js/inspinia.js') }}"></script>
<script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Masked Input -->
<script src="{{ asset('admin/js/jquery.maskedinput.js') }}" charset="utf-8"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    $('input[name=cep]').mask("99.999-999");
    $('input[name=t_pessoa]').on('click',function(){
      if($(this).val() == 'PF'){
        $('input[name=documento]').mask("999.999.999-99");
        $('label[for=documento]').html('CPF');
        $('label[for=cliente]').html('NOME');
      }else{
        $('input[name=documento]').mask("99.999.999/9999-99");
        $('label[for=documento]').html('CNPJ');
        $('label[for=cliente]').html('RAZÃO SOCIAL');
      }
    })
  });
</script>
@endpush
