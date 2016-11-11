<?php $last = App\Models\Pedidos::join('clientes as cl','cl.id','=','pedidos.cliente_id')
              ->whereDate('pedidos.created_at','=',\Carbon\Carbon::Now()->format('Y-m-d'))
              ->select('cl.nome','cl.razao','pedidos.id as idpedido','pedidos.created_at')
              ->orderBy('pedidos.created_at','DESC')
              ->get();
?>

<div id="right-sidebar">
  <div class="sidebar-container">

    <ul class="nav nav-tabs navs-2">

      <li class="active"><a data-toggle="tab" href="#tab-1">
        Vendas
      </a></li>
      <li><a data-toggle="tab" href="#tab-2">
        Clientes
      </a></li>
      <!-- <li class=""><a data-toggle="tab" href="#tab-3">
        <i class="fa fa-gear"></i>
      </a></li> -->
    </ul>

    <div class="tab-content">

      <div id="tab-1" class="tab-pane active">

        <div class="sidebar-title">
          <!-- <h3> <i class="fa"></i>Últimas: {{ \Carbon\Carbon::Now()->format('d/m/Y') }}</h3> -->
          <small><i class="fa fa-tim"></i> Você tem <b>{{ App\Models\Pedidos::whereDate('created_at','=',\Carbon\Carbon::Now()->format('Y-m-d'))->count() }}</b> aprovações no dia de hoje.</small>
        </div>

        <div>
          @foreach($last as $l)

          <div class="sidebar-message">
            <a href="{{ url('/admin/checagem/view/'.$l->idpedido) }}" target="_blank">
              <div class="pull-left text-center">
                <img alt="image" class="img-circle message-avatar" src="{{ asset('img/businessmanavatar.png') }}">
                <!-- <div class="m-t-xs">
                  <i class="fa fa-star text-warning"></i>
                  <i class="fa fa-star text-warning"></i>
                </div> -->
              </div>
              <div class="media-body">
                @if($l->razao == null)
                  {{ strtoupper($l->nome) }}
                @else
                  {{ strtoupper($l->razao) }}
                @endif
                <br>
                <small class="text-muted">{{ $l->created_at->diffForHumans() }}</small>
              </div>
            </a>
          </div>
          @endforeach

        </div>

      </div>

      <div id="tab-2" class="tab-pane">

        <div class="sidebar-title">
          <h3> <i class="fa fa-cube"></i> Latest projects</h3>
          <small><i class="fa fa-tim"></i> You have 14 projects. 10 not completed.</small>
        </div>

        <ul class="sidebar-list">
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Business valuation</h4>
              It is a long established fact that a reader will be distracted.

              <div class="small">Completion with: 22%</div>
              <div class="progress progress-mini">
                <div style="width: 22%;" class="progress-bar progress-bar-warning"></div>
              </div>
              <div class="small text-muted m-t-xs">Project end: 4:00 pm - 12.06.2014</div>
            </a>
          </li>
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Contract with Company </h4>
              Many desktop publishing packages and web page editors.

              <div class="small">Completion with: 48%</div>
              <div class="progress progress-mini">
                <div style="width: 48%;" class="progress-bar"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Meeting</h4>
              By the readable content of a page when looking at its layout.

              <div class="small">Completion with: 14%</div>
              <div class="progress progress-mini">
                <div style="width: 14%;" class="progress-bar progress-bar-info"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="#">
              <span class="label label-primary pull-right">NEW</span>
              <h4>The generated</h4>
              <!--<div class="small pull-right m-t-xs">9 hours ago</div>-->
              There are many variations of passages of Lorem Ipsum available.
              <div class="small">Completion with: 22%</div>
              <div class="small text-muted m-t-xs">Project end: 4:00 pm - 12.06.2014</div>
            </a>
          </li>
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Business valuation</h4>
              It is a long established fact that a reader will be distracted.

              <div class="small">Completion with: 22%</div>
              <div class="progress progress-mini">
                <div style="width: 22%;" class="progress-bar progress-bar-warning"></div>
              </div>
              <div class="small text-muted m-t-xs">Project end: 4:00 pm - 12.06.2014</div>
            </a>
          </li>
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Contract with Company </h4>
              Many desktop publishing packages and web page editors.

              <div class="small">Completion with: 48%</div>
              <div class="progress progress-mini">
                <div style="width: 48%;" class="progress-bar"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="#">
              <div class="small pull-right m-t-xs">9 hours ago</div>
              <h4>Meeting</h4>
              By the readable content of a page when looking at its layout.

              <div class="small">Completion with: 14%</div>
              <div class="progress progress-mini">
                <div style="width: 14%;" class="progress-bar progress-bar-info"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="#">
              <span class="label label-primary pull-right">NEW</span>
              <h4>The generated</h4>
              <!--<div class="small pull-right m-t-xs">9 hours ago</div>-->
              There are many variations of passages of Lorem Ipsum available.
              <div class="small">Completion with: 22%</div>
              <div class="small text-muted m-t-xs">Project end: 4:00 pm - 12.06.2014</div>
            </a>
          </li>

        </ul>

      </div>

      <!-- <div id="tab-3" class="tab-pane">

        <div class="sidebar-title">
          <h3><i class="fa fa-gears"></i> Settings</h3>
          <small><i class="fa fa-tim"></i> You have 14 projects. 10 not completed.</small>
        </div>

        <div class="setings-item">
          <span>
            Show notifications
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="example">
              <label class="onoffswitch-label" for="example">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Disable Chat
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" name="collapsemenu" checked class="onoffswitch-checkbox" id="example2">
              <label class="onoffswitch-label" for="example2">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Enable history
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="example3">
              <label class="onoffswitch-label" for="example3">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Show charts
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="example4">
              <label class="onoffswitch-label" for="example4">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Offline users
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" checked name="collapsemenu" class="onoffswitch-checkbox" id="example5">
              <label class="onoffswitch-label" for="example5">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Global search
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" checked name="collapsemenu" class="onoffswitch-checkbox" id="example6">
              <label class="onoffswitch-label" for="example6">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="setings-item">
          <span>
            Update everyday
          </span>
          <div class="switch">
            <div class="onoffswitch">
              <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="example7">
              <label class="onoffswitch-label" for="example7">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>

        <div class="sidebar-content">
          <h4>Settings</h4>
          <div class="small">
            I belive that. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
            And typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
            Over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
          </div>
        </div>

      </div> -->
    </div>

  </div>



</div>
