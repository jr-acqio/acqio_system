<template>
	<div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
						<div class="form-group col-lg-8">
							<h3>Pesquisar:</h3>
							<input type="text" placeholder="Pesquisar" class="form-control" v-model="filterInput">
						</div>
						<div class="form-group col-lg-4">
							<div class="dropdown">
						    <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Ações
						    <span class="caret"></span></button>
						    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
						      <li role="presentation">
						      	<a role="menuitem" tabindex="-1" href="#" @click.prevent="csv()">Exportar CSV</a>
						      </li>
						      <!-- <li role="presentation" class="divider"></li> -->
						    </ul>
						  </div>
						</div>	
				</div>
				

				<div class="animated fadeInUp">
					<div class="ibox">
						<div class="ibox-content">
							<div class="row m-t-sm">
								<div class="col-lg-12">
									<div class="panel blank-panel">
										<div class="panel-heading">
											<div class="panel-options">
												<ul class="nav nav-tabs">
													<li class="active" @click.prevent="alterTab(1)"><a href="#tab-1" data-toggle="tab">Pagamentos Fda <span class="badge badge-primary">{{ filterByFda((orders.orders_fda),filterInput).length }}</span></a></li>
													<li class="" @click.prevent="alterTab(2)"><a href="#tab-2" data-toggle="tab">Pagamentos Franqueado <span class="badge badge-primary">{{ filterByFranq((orders.orders_fr),filterInput).length }}</span></a></li>
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
																<th><a href="#" @click.prevent="sortFunction('cliente')">Fda</a></th>
																<th><a href="#" @click.prevent="sortFunction('relatorio_pdf')">Relatório PDF</a></th>
																<th><a href="#" @click.prevent="sortFunction('totalVendas')">Total de Vendas</a></th>
																<th><a href="#" @click.prevent="sortFunction('valor')">Valor à pagar</a></th>
																<th><a href="#" @click.prevent="sortFunction('status')">Status</a></th>
																<th>Ações</th>
															</tr>
														</thead>
														<tbody>
															<tr v-for="(order, key) in filterByFda((orders.orders_fda),filterInput)">
																<td>{{ key + 1 }}</td>
																<td>{{ order.cliente }}</td>
																<td>
																	<a target="_blank" :href="order.url">{{ baseName(order.relatorio_pdf) }}</a>
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
																<th><a href="#" @click.prevent="sortFunction('cliente')">Franqueado</a></th>
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
															<tr v-for="(order,key) in filterByFranq((orders.orders_fr),filterInput)">
																<td>{{ key +1 }}</td>
																<td>{{ order.cliente }}</td>
																<td><a target="_blank" :href="order.url">{{ baseName(order.relatorio_pdf) }}</a></td>
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
		orders: {
			orders_fda: [],
			orders_fr: []	
		},
		sortDirection: 1,
		sortProperty: 'cliente',
		filterInput: '',
		filterTab: 1
	}
},
mounted(){
	var self = this
	setTimeout(function(){ self.fetchAllOrders(); }, 1000);
},
		methods:{
			fetchAllOrders(){
				this.$http.get('/admin/orders').then((response) => {
					this.orders.orders_fda = this.filterFda(response.data);
					this.orders.orders_fr = this.filterFranq(response.data);
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
				var base = new String(str).substring(str.lastIndexOf('/') + 1); 
			  return base;
			},
			sortFunction(field){
				this.sortProperty = field
				if(this.sortDirection == 1){
					if(this.filterTab == 1){
						this.orders.orders_fda = _.sortBy( this.orders.orders_fda, field );	
					}else{
						this.orders.orders_fr = _.sortBy( this.orders.orders_fr, field );	
					}
 					this.sortDirection = -1
				}else{
					this.sortDirection = 1
					if(this.filterTab == 1){
						this.orders.orders_fda = _.sortBy( this.orders.orders_fda, field ).reverse();	
					}else{
						this.orders.orders_fr = _.sortBy( this.orders.orders_fr, field ).reverse();	
					}
				}
			},
			filterByFda(list,value){
				// if(this.filterTab != 1){
				// 	return false;
				// }
				value = value.toUpperCase();
				// alert('filtrando fdas');
				return list.filter(function(order){
					return order.cliente.indexOf(value) > -1 || order.relatorio_pdf.indexOf(value) > -1 || order.valor.toString().indexOf(value) > -1 || order.totalVendas.toString().indexOf(value) > -1;
				});
			},
			filterByFranq(list,value){
				// if(this.filterTab != 2){
				// 	return false;
				// }
				value = value.toUpperCase();
				// alert('filtrando franqueados');
				return list.filter(function(order){
					return order.cliente.indexOf(value) > -1 || order.relatorio_pdf.indexOf(value) > -1 || order.valor.toString().indexOf(value) > -1 || order.totalVendas.toString().indexOf(value) > -1 || order.totalRoyaltie.toString().indexOf(value) > -1;
				});	
			},
			alterTab(int){
				this.filterTab = int
			},
			csv(){
				downloadCSV({filename: 'ordens_pagamento.csv'}, this.ordersFilter);
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
	.dropdown{
		margin-top: 30px;
		float: left;
	}
</style>


