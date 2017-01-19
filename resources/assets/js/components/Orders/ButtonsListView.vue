<template>
	<div>
		<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
	    					<router-link class="btn btn-success" :to="{ name: 'orders-list', query: { type: 'pay' }}" v-if="this.$route.query.type != 'pay'">
	    						<b>Realizados</b>
	    					</router-link>
	  						
								<router-link class="btn btn-default" to="/admin/orders/list" v-if="$route.query.type =='pay'">
									<b>Pendentes</b>
					    	</router-link>	  							

					    	<router-link class="btn btn-danger" to="/admin/orders/list/rejected" >
									<b>Rejeitados</b>
					    	</router-link>


					    	<button class="btn btn-primary ladda-button ladda-button-demo pull-right" data-style="expand-right" @click.prevent="atualizar()"><b>Atualizar</b></button>

                <div class="sk-spinner sk-spinner-three-bounce" v-show="isloading">
                    <div class="sk-bounce1"></div>
                    <div class="sk-bounce2"></div>
                    <div class="sk-bounce3"></div>
                </div>

						 </div>
					</div>					

				</div>
	</div>
</template>

<script>
export default{
	data(){
		return {
			isloading: false
		}
	},
	methods: {
		atualizar(){
			this.isloading = true
			var self = this;
			var l = Ladda.create( document.querySelector( '.ladda-button-demo' ) );	
			l.start();
			setTimeout(function(){
				self.$emit('fetchAllOrders');	
				l.stop();
				self.isloading = false
			},1000)
		}
	},
	mounted(){

	},
	watch: {

    '$route' (to, from) {
      // react to route changes...
      // console.log(this.$route.query)
      // console.log(to)
      // this.query = to.query.type
    }
  }
}

</script>
<style></style>