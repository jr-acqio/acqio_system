<nav class="navbar-default navbar-static-side" role="navigation">
  <div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
      <li class="nav-header">
        <div class="dropdown profile-element"> <span>
          <a href="{{ url('/admin/dashboard') }}">
            <img alt="image" class="img-responsive" src="{{ asset('img/acqio-login.png') }}" />
          </a>
        </span>
        </div>
        <div class="logo-element">
          <img alt="image" class="img-responsive" src="{{ asset('img/acqio-login.png') }}" />
        </div>
      </li><br>
      <li class="{{ Request::is('admin/dashboard') ? 'active' : null }}">
        <a href="{{ url('/admin/dashboard') }}"><i class="fa fa-home"></i><span class="nav-label"> Início</span></a>
      </li>
      <!-- <li><a href="#"><i class="fa fa-database" aria-hidden="true"></i><span class="nav-label"> Banco</span></a></li> -->
      <li class="{{ Request::is('admin/produtos') ? 'active' : null }}"><a href="{{ url('/admin/produtos') }}"><i class="fa fa-list-alt" aria-hidden="true"></i><span class="nav-label"> Produtos</span></a></li>
      <li class="{{ Request::is('admin/clientes') ? 'active' : null }}">
        <a href="#"><i class="fa fa-users" aria-hidden="true"></i><span class="nav-label"> Cadastros</span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <li class=""><a href="{{ URL('admin/clientes/clientes') }}"> Cliente</a></li>
            <li>
                <a href="#">Franqueado<span class="fa arrow"></span></a>
                <ul class="nav nav-third-level">
                  <li>
                      <a href="{{ route('admin.franqueado.index') }}">Cadastrados</a>
                  </li>
                    <li>
                        <a href="{{ route('admin.franqueado.create') }}">Cadastrar Franqueado</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">Fda<span class="fa arrow"></span></a>
                <ul class="nav nav-third-level">
                  <li>
                      <a href="{{ route('admin.fda.index') }}">Cadastrados</a>
                  </li>
                    <li>
                        <a href="{{ route('admin.fda.create') }}">Cadastrar Fda's</a>
                    </li>
                </ul>
            </li>
            <!-- <li class=""><a href="#"> FRANQUEADO</a></li> -->
            <!-- <li class=""><a href="#"> FDA</a></li> -->
        </ul>
      </li>
      <li class="{{ Request::is('admin/imports*') ? 'active' : null }}">
        <a href="#"><i class="fa fa-file-excel-o"></i><span class="nav-label">Pagamentos</span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <li class=""><a href="{{ URL::route('imports/import-transacoes') }}"><i class="fa fa-credit-card"></i> Transações</a></li>
            <li class=""><a href="{{ URL::route('imports/import-boleto') }}"><i class="fa fa-file-pdf-o"></i> Boletos</a></li>
        </ul>
      </li>
      <li class="{{ Request::is('admin/checagem*') ? 'active' : null }}">
        <a href="#"><i class="fa fa-cart-plus"></i><span class="nav-label">Pedidos</span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Request::is('admin/checagem') ? 'active' : null }}"><a href="{{ URL('/admin/checagem') }}">Registrar Venda</a></li>
            <!-- <li class="{{ Request::is('admin/checagem/consulta') ? 'active' : null }}"><a href="{{ URL('/admin/checagem/consulta') }}">Consultar Pedido</a></li> -->
            <li class="{{ Request::is('admin/checagem/listar-pedido') ? 'active' : null }}"><a href="{{ URL('/admin/checagem/listar-pedido') }}">Consultar Pedidos</a></li>
        </ul>
      </li>
      <li class="{{ Request::is('admin/comissoes*') || Request::is('admin/orders*') ? 'active' : null }}">
        <a href="#"><i class="fa fa-line-chart" aria-hidden="true"></i> <span class="nav-label">Comissões</span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Request::is('admin/comissoes/create') ? 'active' : null }}"><a href="{{ route('admin.comissoes.create') }}">Importar Comissões</a></li>
            <li class="{{ Request::is('admin/comissoes') ? 'active' : null }}"><a href="{{ route('admin.comissoes.index') }}">Consultar Comissões</a></li>
            <li class="{{ Request::is('admin/comissoes/list*') ? 'active' : null }}"><a href="{{ url('/admin/comissoes/list') }}">Listar Comissão</a></li>
            <li class="{{ Request::is('admin/orders*') ? 'active' : null }}"><a href="{{ route('admin.orders.index') }}">Ordens de Pagamento</a></li>
        </ul>
      </li>
      <li class="{{ Request::is('admin/royalties*') ? 'active' : null }}">
        <a href="#"><i class="fa fa-money" aria-hidden="true"></i> <span class="nav-label">Royalties</span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Request::is('admin/royalties/create') ? 'active' : null }}"><a href="{{ route('admin.royalties.create') }}">Importar Royalties <i class="fa fa-upload" aria-hidden="true"></i></a></li>
            <li class="{{ Request::is('admin/royalties') ? 'active' : null }}"><a href="{{ url('/admin/royalties/') }}">Visualizar Royalties <i class="fa fa-eye" aria-hidden="true"></i></a></li>
        </ul>
      </li>
    </ul>

  </div>
</nav>
