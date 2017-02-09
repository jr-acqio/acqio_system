<template>
	<div>

		<div class="row wrapper border-bottom white-bg page-heading" style="margin: 1px 1px;">
		  <div class="col-lg-12">
		    <h2>Ordens de Pagamento (Comissões)</h2>
		    <ol class="breadcrumb">
		      <li>
		        <a href="/admin/dashboard">Home</a>
		      </li>
		      <li class="active">
		        <a href="/admin/orders/list"><strong>Orders</strong></a>
		      </li>
		    </ol>
		  </div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="form-group col-lg-4">
						<h3>Pesquisar:</h3>
						<input type="text" placeholder="Pesquisar" class="form-control" v-model="filterInput">
					</div>
					<div class="form-group col-lg-3">
						<h3 for="">Filtrar por Mês</h3>
						<select name="" id="" class="form-control" v-model="filterMonth">
							<option value="">Nenhum</option>
							<option value="1">Janeiro</option>
							<option value="2">Fevereiro</option>
							<option value="3">Março</option>
							<option value="4">Abril</option>
							<option value="5">Maio</option>
							<option value="6">Junho</option>
							<option value="7">Julho</option>
							<option value="8">Agosto</option>
							<option value="9">Setembro</option>
							<option value="10">Outubro</option>
							<option value="11">Novembro</option>
							<option value="12">Dezembro</option>
						</select>
					</div>
				</div>

				<buttons-list-view v-on:fetchAllOrders="fetchAllOrders()"></buttons-list-view>

				
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
																	<p v-if="order.status" class="pg">Pago <i class="glyphicon glyphicon-ok"></i> </p>
																	<p v-else>Processando</p>
																</td>
																<td>
																	<a :href="'/admin/orders/'+order.id" class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a>
																	<a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top" @click.prevent="approvedOrder(order,orders.orders_fda)" v-if="order.status != 1">
																		<i class="fa fa-thumbs-up" aria-hidden="true"></i>
																	</a>
																	<a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top" v-if="order.status != 1">
																		<i class="fa fa-thumbs-down" aria-hidden="true"></i>
																	</a>
																	<a href="#" class="btn btn-default btn-xs" title="Pendenciar" data-toggle="tooltip" data-placement="top" v-if="order.status == 1" @click.prevent="neutralizeOrder(order,orders.orders_fda)">
																		<i class="fa fa-reply" aria-hidden="true"></i>
																	</a>
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
																<th><a href="#" @click.prevent="sortFunction('relatorio_pdf')">Relatório PDF</a></th>
																<th><a href="#" @click.prevent="sortFunction('totalVendas')">Total Vendas</a></th>
																<!-- <th>Total Comissão</th> -->
																<th><a href="#" @click.prevent="sortFunction('totalRoyaltie')">Royalties</a></th>
																<th><a href="#" @click.prevent="sortFunction('valor')">Liq. à pagar</a></th>
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
																	<p v-if="order.status" class="pg">Pago <i class="glyphicon glyphicon-ok"></i> </p>
																	<p v-else>Processando</p>
																</td>
																<td>
																	<a :href="'/admin/orders/'+order.id" class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a>
																	
																	<a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top" @click.prevent="approvedOrder(order,orders.orders_fr)" v-if="order.status != 1">
																		<i class="fa fa-thumbs-up" aria-hidden="true"></i>
																	</a>
																	<a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top" v-if="order.status != 1">
																		<i class="fa fa-thumbs-down" aria-hidden="true"></i>
																	</a>
																	<a href="#" class="btn btn-default btn-xs" title="Pendenciar" data-toggle="tooltip" data-placement="top" v-if="order.status == 1" @click.prevent="neutralizeOrder(order,orders.orders_fr)">
																		<i class="fa fa-reply" aria-hidden="true"></i>
																	</a>
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
	import ButtonsListView from './ButtonsListView.vue'
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
				filterMonth: '',
				filterTab: 1
			}
		},
		components: {
			ButtonsListView
		},
		mounted(){
			var self = this
			setTimeout(function(){ self.fetchAllOrders(); }, 1000);
		},
		watch: {
    '$route' (to, from) {
      // react to route changes...
      // console.log(to)
      this.fetchAllOrders()
    }
  },
		methods:{
			fetchAllOrders(){
				if(this.$route.query.type == 'pay'){
					this.$http.get('/admin/orders?type=pay').then((response) => {
					this.orders.orders_fda = _.orderBy(this.filterFda(response.data),['valor'],['desc']);
					this.orders.orders_fr = _.orderBy(this.filterFranq(response.data),['valor'],['desc']);
					}, (response) => {
						iziToast.show({
							title: 'Error:',
							message: 'Houve algum erro ao carregar as ordens de pagamento :(',
				   				color: 'red', // blue, red, green, yellow,
				   				position: 'bottomLeft'
				   			});
					});
				}else{
					this.$http.get('/admin/orders').then((response) => {
					this.orders.orders_fda = _.orderBy(this.filterFda(response.data),['valor'],['desc']);
					this.orders.orders_fr = _.orderBy(this.filterFranq(response.data),['valor'],['desc']);
					}, (response) => {
						iziToast.show({
							title: 'Error:',
							message: 'Houve algum erro ao carregar as ordens de pagamento :(',
				   				color: 'red', // blue, red, green, yellow,
				   				position: 'bottomLeft'
				   			});
					});
				}
				
			},
			approvedOrder(order,list){
					//Verificar se o status já é de aprovado.
					if(order.status){
						iziToast.show({
							title: 'Error:',
							message: 'Este pagamento já foi realizado!!',
		   				color: 'red', // blue, red, green, yellow,
		   				position: 'bottomLeft'
		   			});	
						return false;
					}
					this.$http.put('/admin/orders/'+order.id, {params: {type: 'approvedOrder'} }).then(response => {
						//Atualizar no cliente
						let index = list.indexOf(order)
						list[index]['status'] = 1
						//Alert message success update
						iziToast.show({
							title: 'Updated Sucessfully:',
							message: '#OrderID: '+order.id+'. Pagamento realizado!!',
		   				color: 'green', // blue, red, green, yellow,
		   				position: 'bottomLeft'
		   			});	
					}),(response) =>{
						iziToast.show({
							title: 'Error:',
							message: 'Houve algum erro ao atualizar esta ordem de pagamento :(',
			   				color: 'red', // blue, red, green, yellow,
			   				position: 'bottomLeft'
			   			});
					}
				},
				neutralizeOrder(order,list){
					this.$http.put('/admin/orders/'+order.id, {params: {type: 'neutralizeOrder'} }).then(response => {
						//Atualizar no cliente
						let index = list.indexOf(order)
						list[index]['status'] = 0
						//Alert message success update
						iziToast.show({
							title: 'Updated Sucessfully:',
							message: '#OrderID: '+order.id+'. Alterado para Pendente!!',
		   				color: 'green', // blue, red, green, yellow,
		   				position: 'bottomLeft'
		   			});	
					}),(response) =>{
						iziToast.show({
							title: 'Error:',
							message: 'Houve algum erro ao atualizar esta ordem de pagamento :(',
			   				color: 'red', // blue, red, green, yellow,
			   				position: 'bottomLeft'
			   			});
					}
				},
			//Filters
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
			filterByMonth(list){
				if(this.filterMonth == ""){
					return list;
				}
				var self = this
				return _.filter(list, function(o){
					return o.mes_ref == self.filterMonth
				});
			},
			filterByFda(list,value){
				value = value.toUpperCase();	
				return _.filter(this.filterByMonth(list),function(order){
					return order.cliente.indexOf(value) > -1 || order.relatorio_pdf.indexOf(value) > -1 || order.valor.toString().indexOf(value) > -1 || order.totalVendas.toString().indexOf(value) > -1;
				});
			},
			filterByFranq(list,value){
				value = value.toUpperCase();
				return this.filterByMonth(list).filter(function(order){
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
		font-weight: bold;
	}
	.rejected{
		color: red;
	}
	.dropdown{
		margin-top: 30px;
		float: left;
	}
</style>


