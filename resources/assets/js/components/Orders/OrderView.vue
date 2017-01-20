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
								<h4>Invoice No.</h4>
								<h4 class="text-navy">INV-{{ $route.params.orderid }}</h4>
								<span>Para:</span>
								<address>
									<strong></strong><br>
									112 Street Avenu, 1080<br>
									Miami, CT 445611<br>
									<abbr title="Phone">P:</abbr> (120) 9000-4321
								</address>
								<p>
									<span><strong>Invoice Date:</strong> Marh 18, 2014</span><br>
									<span><strong>Due Date:</strong> March 24, 2014</span>
								</p>
							</div>
						</div>

						<div class="table-responsive m-t">
							<table class="table invoice-table">
								<thead>
									<tr>
										<th>Clientes</th>
										<th>Quantity</th>
										<th>Unit Price</th>
										<th>Tax</th>
										<th>Total Price</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="item in order.vendas">
										<td><div><strong>{{ item.nome_cliente.toUpperCase() }}</strong></div>
											<small>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</small>
										</td>
										<td>1</td>
										<td>$26.00</td>
										<td>$5.98</td>
										<td>$31,98</td>
									</tr>
								</tbody>
							</table>
						</div><!-- /table-responsive -->

						<table class="table invoice-total">
							<tbody>
								<tr>
									<td><strong>Sub Total :</strong></td>
									<td>$1026.00</td>
								</tr>
								<tr>
									<td><strong>TAX :</strong></td>
									<td>$235.98</td>
								</tr>
								<tr>
									<td><strong>TOTAL :</strong></td>
									<td>$1261.98</td>
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
				order: ''
			}
		},
		mounted(){
			this.order = JSON.parse(this.order_prop)
			for (var i = 0; i < this.order[0].vendas.length; i++) {
				console.log(this.order[0].vendas[i].nome_cliente)
				alert(this.order[0].vendas[i].nome_cliente)
			}
			console.log(Object.keys(this.order.vendas).length)
		},
		methods: {

		}
	}
</script>
<style scoped=""></style>