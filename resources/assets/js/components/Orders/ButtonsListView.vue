<template>
	<div>
		<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
	    					<router-link class="btn btn-success" to="/admin/orders/list/pay" v-if="dad != 'OrdersPay'">
	    						Realizados
	    					</router-link>
	  						
								<router-link class="btn btn-default" to="/admin/orders/list" v-if="dad != 'OrdersList'">
									Pendentes
					    	</router-link>	  							

					    	<router-link class="btn btn-danger" to="/admin/orders/list/rejected" >
									Rejeitados
					    	</router-link>


					    	<button class="btn btn-primary pull-right" @click.prevent="atualizar()">Atualizar <i class="fa fa-refresh"></i></button>
						 </div>
					</div>					

				</div>
	</div>
</template>

<script>
export default{
	props:{
		dad:{
			required: true,
			type: String
		}
	},
	data(){
		return{

		}
	},
	methods: {
		atualizar(){
			this.$emit('fetchAllOrders');
		}
	}
}

</script>
<style></style>