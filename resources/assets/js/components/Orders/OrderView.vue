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
								<address>
									<strong>{{ order.cliente }}</strong><br>
									{{ order.endereco }}<br>
									{{ order.cidade }}<br>
									<abbr title="Phone">Email:</abbr> {{ order.email }}
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
									<tr v-for="item in order.vendas">
										<td>{{ item.data_cadastro }}</td>
										<td>{{ item.data_aprovacao }}</td>
										<td><div><strong>{{ item.nome_cliente.toUpperCase() }}</strong></div>
											<small v-if="item.uf != null && item.cidade != null">{{ item.uf + ' - ' + item.cidade }}</small>
										</td>
										<td>{{ item.produtos.length }}</td>
										<td>
											<span v-for="produto in item.produtos">{{ produto.descricao }} </span>
										</td>
										<!-- <td>$5.98</td> -->
										<td>R$ {{ sum(item.produtos) }}</td>
									</tr>
								</tbody>
							</table>
						</div><!-- /table-responsive -->

						<table class="table invoice-total">
							<tbody>
								<tr>
									<td><strong>Sub Total (Comissões) :</strong></td>
									<td>R$ {{  }}</td>
								</tr>
								<tr>
									<td><strong>Sub Total (Royalties) :</strong></td>
									<td>R$ {{ order.totalRoyaltie }}</td>
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

						<div class="well m-t"><strong>Comments</strong>
							It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
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
				endDate: ''
			}
		},
		mounted(){
			this.order = JSON.parse(this.order_prop)
			var startDate = moment([2016, this.order.mes_ref-1]).add(+1,"month");
			this.startDate = new Date(2016, this.order.mes_ref, 1)
			var endDate = moment(startDate, 'YYYY-MM-DD').endOf('month');
			this.endDate= endDate;
		    // console.log(startDate.toObject().date +'/'+ startDate.toObject().months +'/'+ startDate.toObject().years);
    		// console.log(endDate.toObject().date +'/'+ endDate.toObject().months +'/'+ endDate.toObject().years);
		},
		methods: {
			sum( obj ) {
			  var sum = 0;
			  for(var i = 0; i< obj.length ; i++){
			  	if(this.order.fdaid == null){
			  		sum += parseFloat(obj[i].tx_venda)
			  	}else{
			  		sum += parseFloat(obj[i].tx_install)
			  	}
			  }
			  return sum;
			}	
		}
	}
</script>
<style scoped=""></style>