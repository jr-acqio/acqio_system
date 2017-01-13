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
															<tr v-for="order in filterFda(orders)">
																<td>{{ order.id }}</td>
																<td>{{ order.mes_ref }}</td>
																<td>
																	<a href="#">
																		{{ baseName(order.relatorio_pdf) }}
																	</a>
																</td>
																<td></td>
																<td></td>
																<td></td>
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
															<tr v-for="order in filterFranq(orders)">
																<td>{{ order.id }}</td>
																<td>{{ order.mes_ref }}</td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>
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
					console.log(this.orders[0].relatorio_pdf)
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
			   console.log(base);
			    // if(base.lastIndexOf(".") != -1)       
			    //     base = base.substring(0, base.lastIndexOf("."));
			   return base;
			}
		}
	}
</script>
<style scoped=""></style>


