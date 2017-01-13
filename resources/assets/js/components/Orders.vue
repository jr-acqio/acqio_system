<template>
	<div>
		<h1>Estou dentro do Component</h1>
		<div class="row">
			<div class="col-lg-12">
				<div class="animated fadeInUp">
					<div class="ibox">
						<div class="ibox-content">
							<div class="row m-t-sm">
								<div class="col-lg-12">
									<div class="panel blank-panel">
										<div class="panel-heading">
											<div class="panel-options">
												<ul class="nav nav-tabs">
													<li class="active"><a href="#tab-1" data-toggle="tab">Pagamentos Fda <span class="badge badge-primary">{{ filterFda(orders).length }}</span></a></li>
													<li class=""><a href="#tab-2" data-toggle="tab">Pagamentos Franqueado <span class="badge badge-primary">{{ filterFranq(orders).length }}</span></a></li>
												</ul>
											</div>
										</div>
										<div class="panel-body">

											<div class="tab-content">
												<div class="tab-pane active table-responsive" id="tab-1">
													<table class="table table-striped table-hover">
														<thead>
															<tr>
																<th>#</th>
																<th>Fda</th>
																<th>Relatório PDF</th>
																<th>Total de Vendas</th>
																<th>Valor à pagar</th>
																<th>Status</th>
																<th>Ações</th>
															</tr>
														</thead>
														<tbody>
															<tr v-for="(order, key) in filterFda(orders)">
																<td>{{ key + 1 }}</td>
																<td>{{ order.cliente }}</td>
																<td>
																	{{ baseName(order.relatorio_pdf) }}
																</td>
																<td>{{ order.totalVendas }}</td>
																<td>R$ {{ order.valor.formatMoney(2,',','.') }}</td>
																<td>
																	<p v-if="order.status" class="pg">Pago</p>
																	<p v-else>Processando</p>
																</td>
																                            <td>
                              <a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
                              <a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
                            </td>

															</tr>
														</tbody>
													</table>

												</div>
												<div class="tab-pane table-responsive" id="tab-2">
													<table class="table table-striped table-hover">
														<thead>
															<tr>
																<th>#</th>
																<th>Identificador Franqueado</th>
																<th>Relatório PDF</th>
																<th>Total Vendas</th>
																<!-- <th>Total Comissão</th> -->
																<th>Royalties</th>
																<th>Liq. à pagar</th>
																<th>Status</th>
																<th>Ações</th>
															</tr>
														</thead>
														<tbody>
															<tr v-for="(order,key) in filterFranq(orders)">
																<td>{{ key +1 }}</td>
																<td>{{ order.cliente }}</td>
																<td>{{ baseName(order.relatorio_pdf) }}</td>
																<td>{{ order.totalVendas }}</td>
																<td>
																	<p v-if="order.totalRoyaltie > 0" class="rejected">
																		R$ {{ order.totalRoyaltie.formatMoney(2,',','.') }}
																	</p>
																	<p v-else>R$ {{ order.totalRoyaltie.formatMoney(2,',','.') }}</p>

																</td>
																<td>R$ {{ order.valor.formatMoney(2,',','.') }}</td>
																<td>
																	<p v-if="order.status" class="pg">Pago</p>
																	<p v-else>Processando</p>
																</td>
																<td>
																	<a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
																	<a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
																</td>

															</tr>
														</tbody>
														<tfoot>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import _ from 'lodash'
	export default{
		data(){
	return {
		orders: []
	}
},
mounted(){
	var self = this
	setTimeout(function(){ self.fetchAllOrders(); }, 1000);


			// this.baseName(this.orders[0].relatorio_pdf);
		},
		methods:{
			fetchAllOrders(){
				this.$http.get('/admin/orders').then((response) => {
					this.orders = response.data;
					iziToast.show({
						title: 'Load Sucessfully',
						message: 'Ordens de Pagamento carregadas com sucesso!!',
		   				color: 'green', // blue, red, green, yellow,
		   				position: 'topRight'
		   			});
				}, (response) => {
					iziToast.show({
						title: 'Error:',
						message: 'Houve algum erro ao carregar as ordens de pagamento :(',
		   				color: 'red', // blue, red, green, yellow,
		   				position: 'topRight'
		   			});
				});
			},
			filterFranq(orders){
				return _.filter(orders, function(o) {
					return o.fdaid == null;
				});
			},
			filterFda(orders){
				return _.filter(orders, function(o){
					return o.franqueadoid == null;
				})
			},
			baseName(str)
			{
				// console.log(str)
				var base = new String(str).substring(str.lastIndexOf('/') + 1); 
			    // if(base.lastIndexOf(".") != -1)       
			    //     base = base.substring(0, base.lastIndexOf("."));
			    return base;
			}
		}
	}
</script>
<style scoped="">
	.pg{
		color: green;
	}
	.rejected{
		color: red;
	}

</style>


