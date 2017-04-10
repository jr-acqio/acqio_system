<template>
	<div>
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-6">
				<h2>Pagamento</h2>
				<ol class="breadcrumb">
					<li>
						<a href="/">Home</a>
					</li>
					<li>
						<a href="/admin/orders/list">Lista de Pagamentos</a>
					</li>
					<li class="active">
						<strong>Pagamento - {{ order.id }}</strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-6">
				<div class="title-action">
					<a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Editar </a>
					<a href="#" class="btn btn-white"><i class="fa fa-check "></i> Salvar </a>
					<a target="_blank" :href="'/admin/orders/'+order.id+'/'+baseName(order.relatorio_pdf)" class="btn btn-primary"><i class="fa fa-eye"></i> Visualizar Relatório </a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="wrapper wrapper-content animated fadeInRight">
					<div class="ibox-content p-xl">
						<div class="row">
							<div class="col-sm-6">
								<h5>De:</h5>
								<address>
									<strong>Esfera 5 Tecnologia em Pagamentos S.A.</strong><br>
									Rua: Domingos José Martins, 75, Sala 304<br>
									Recife Antigo - Recife/PE, CEP: 50030-200<br>
									<abbr title="Phone">P:</abbr> (81) 3224-9130
								</address>
							</div>

							<div class="col-sm-6 text-right">
								<h4>Pagamento No. <span class="text-navy">{{ order.id }}</span></h4>
								<span>Para:</span>
								<address v-if="order.fda != null">
									<strong>{{ order.fda.nome_razao }}</strong><br>
									{{ order.fda.endereco }}<br>
									{{ order.fda.cidade }}<br>
									<abbr title="Phone">Email:</abbr> {{ order.fda.email }}
								</address>
								<address v-if="order.franqueado != null">
									<strong>{{ order.franqueado.nome_razao }}</strong><br>
									{{ order.franqueado.endereco }}<br>
									{{ order.franqueado.cidade }}<br>
									<abbr title="Phone">Email:</abbr> {{ order.franqueado.email }}
								</address>
								<p>
									<span><strong>Data inicial:</strong> {{ startDate }}</span><br>
									<span><strong>Data final:</strong> {{ endDate }}</span>
								</p>
							</div>
						</div>

						<div class="table-responsive m-t">
							<table class="table invoice-table table-hover">
								<thead>
									<tr>
										<th>Data Cadastro</th>
										<th>Data Aprovação</th>
										<th>Clientes</th>
										<th>Quantidade</th>
										<th>Modelos</th>
										<!-- <th>Tax</th> -->
										<th>Valor Total</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="item in order.comissoes">
										<td>{{ item.data_cadastro }}</td>
										<td>{{ item.data_aprovacao }}</td>
										<td><div><strong>{{ item.nome_cliente.toUpperCase() }}</strong></div>
											<small v-if="item.uf != 'nu' && item.uf != 'null' || item.cidade != 'null'">{{ item.uf + ' - ' + item.cidade }}</small>
										</td>
										<td>{{ item.produtos.length }}</td>
										<td>
											<span v-for="produto in item.produtos">{{ produto.descricao }} </span>
										</td>
										<!-- <td>$5.98</td> -->
										<td>R$
											<span v-if="order.franqueado == null">
												{{ sum(item.produtos,'tx_install').formatMoney(2,',','.') }}
											</span>
											<span v-else>
												{{ sum(item.produtos,'tx_venda').formatMoney(2,',','.') }}
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- /table-responsive -->


						<div class="row">
							<div class="col-lg-6">
								<div v-if="totalRoyaltie > 0">
									<a v-if="listDebitos == false" @click="listDebitos = true" class="btn btn-default">Exibir Débitos
										<i class="fa fa-plus"></i>
									</a>
									<a v-else="" @click="listDebitos = false" class="btn btn-default">
										Esconder Débitos
										<i class="fa fa-minus"></i>
									</a>
								</div>

								<br><br>
								<!-- Table Descontos -->
								<table class="table table-hover" v-if="listDebitos == true">
									<thead>
										<!-- <th>Id</th> -->
										<th>Data Vencimento</th>
										<th>Valor Royalties</th>
										<th>Cheques Devolv.</th>
										<th>Franquia/Localização</th>
									</thead>
									<tbody>
										<tr v-for="royaltie in order.royalties">
											<!-- <td>{{ royaltie.id }}</td> -->
											<td>{{ royaltie.data_vencimento }}</td>
											<td>{{ royaltie.valor_original.formatMoney(2,',','.') }}</td>
											<td>{{ royaltie.cheques_devolvidos.formatMoney(2,',','.') }}</td>
											<td>{{ royaltie.franquia_loc }}</td>
										</tr>
									</tbody>
								</table>

							</div>
							<div class="col-lg-6">
								<table class="table invoice-total">
									<tbody>
										<tr>
											<td><strong>Sub Total (Comissões) :</strong></td>
											<td>R$ {{ totalLiq }}</td>
										</tr>
										<tr v-if="totalRoyaltie > 0">
											<td>
												<strong>Sub Total (Royalties) :</strong>
											</td>
											<td style="color:red;">- R$ {{ sum(order.royalties,'valor_original').formatMoney(2,',','.') }}</td>
										</tr>
										<tr>
											<td><strong>TOTAL :</strong></td>
											<td>R$ {{ order.valor }}</td>
										</tr>
									</tbody>
								</table>



								<div class="text-right">
									<button v-if="order.status != 1" class="btn btn-primary" @click.prevent="approvedOrder(order)"><i class="fa fa-dollar"></i> Pagamento Realizado</button>

									<button v-else="" class="btn btn-default" @click.prevent="neutralizeOrder(order)"><i class="fa fa-reply"></i> Pendenciar Pagamento</button>
								</div>
							</div>
						</div>


						<div class="well m-t"><strong>Situação: </strong>
							<span v-if="order.status == 1" style="color:green"><strong>Pago <i class="fa fa-check"></i></strong></span>
							<span v-else="" style="color:red"><strong>Pagamento Pendente!!</strong></span>
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
		props: ['order_prop'],
		data(){
			return {
				order: '',
				startDate: '',
				endDate: '',
				totalRoyaltie: '',
				totalLiq: '',

				listDebitos: false
			}
		},
		mounted(){
			this.order = JSON.parse(this.order_prop)


			this.totalRoyaltie = this.sum(this.order.royalties,'valor_original')

			var total = 0;
			for(var i = 0; i < this.order.comissoes.length ; i++){
				for(var j = 0 ; j < this.order.comissoes[i].produtos.length ; j++){
					if(this.order.fdaid == null){
						total += parseFloat(this.order.comissoes[i].produtos[j].tx_venda)
					}else{
						total += parseFloat(this.order.comissoes[i].produtos[j].tx_install)
					}
				}
			}
			this.totalLiq = total;
			console.log(this.totalLiq);
		},
		methods: {
			sum( obj,element ) {
				// console.log(obj,element)
				var sum = 0;
				for(var i = 0; i < obj.length ; i++){
					if(this.order.fdaid == null){
						sum += parseFloat(obj[i][element])
					}else{
						sum += parseFloat(obj[i][element])
					}
				}
				return sum;
			},
			baseName(str)
			{
				var base = new String(str).substring(_.lastIndexOf(str,'/') + 1);
				return base;
			},
			approvedOrder(order){
				this.$http.put('/admin/orders/'+order.id, {params: {type: 'approvedOrder'} }).then(response => {
					order.status = 1
					swal("Feito!", "Pagamento de comissão realizado!", "success")
				}),(response) =>{
					iziToast.show({
						title: 'Error:',
						message: 'Houve algum erro ao atualizar esta ordem de pagamento :(',
		   				color: 'red', // blue, red, green, yellow,
		   				position: 'bottomLeft'
		   			});
				}
			},
			neutralizeOrder(order){
				this.$http.put('/admin/orders/'+order.id, {params: {type: 'neutralizeOrder'} }).then(response => {
					//Atualizar no cliente
					order.status = 0
					//Alert message success update
					swal("Feito!", "Pagamento de comissão pendenciado!", "success")
				}),(response) =>{
					iziToast.show({
						title: 'Error:',
						message: 'Houve algum erro ao atualizar esta ordem de pagamento :(',
		   				color: 'red', // blue, red, green, yellow,
		   				position: 'bottomLeft'
		   			});
				}
			}
		}
	}
</script>
<style scoped=""></style>
