<template>
	<div>
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-8">
				<h2>Pagamento - {{ order.id }}</h2>
				<ol class="breadcrumb">
					<li>
						<a href="/">Home</a>
					</li>
					<li>
						Other Pages
					</li>
					<li class="active">
						<strong></strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-4">
				<div class="title-action">
					<a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Editar </a>
					<a href="#" class="btn btn-white"><i class="fa fa-check "></i> Salvar </a>
					<a href="#" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir Resumo </a>
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
									<span><strong>Invoice Date:</strong> {{ startDate }}</span><br>
									<span><strong>Due Date:</strong> {{ endDate }}</span>
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
											<small v-if="item.uf != null && item.cidade != null">{{ item.uf + ' - ' + item.cidade }}</small>
										</td>
										<td>{{ item.produtos.length }}</td>
										<td>
											<span v-for="produto in item.produtos">{{ produto.descricao }}</span>
										</td>
										<!-- <td>$5.98</td> -->
										<td>R$ 
											<span v-if="order.franqueado == null">
												{{ sum(item.produtos,'tx_install') }}
											</span>
											<span v-else>
												{{ sum(item.produtos,'tx_venda') }}
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- /table-responsive -->

						<table class="table invoice-total">
							<tbody>
								<tr>
									<td><strong>Sub Total (Comissões) :</strong></td>
									<td>R$ {{ totalLiq }}</td>
								</tr>
								<tr v-if="totalRoyaltie > 0">
									<td><strong>Sub Total (Royalties) :</strong></td>
									<td style="color:red;">- R$ {{ totalRoyaltie }}</td>
								</tr>
								<tr>
									<td><strong>TOTAL :</strong></td>
									<td>R$ {{ order.valor }}</td>
								</tr>
							</tbody>
						</table>
						<div class="text-right">
							<button class="btn btn-primary"><i class="fa fa-dollar"></i> Make A Payment</button>
						</div>

						<div class="well m-t"><strong>Comments </strong>

							<!-- {{ sum( order.valor , 'valor_original' ) }} -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default{
		props: ['order_prop'],
		data(){
			return {
				order: '',
				startDate: '',
				endDate: '',
				totalRoyaltie: '',
				totalLiq: ''
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
			}
		}
	}
</script>
<style scoped=""></style>