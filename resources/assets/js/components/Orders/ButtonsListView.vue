<template>
	<div>
		<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
	    					<router-link class="btn btn-success" :to="{ name: 'orders-list', query: { type: 'pay' }}" v-if="this.$route.query.type != 'pay'">
	    						Realizados
	    					</router-link>
	  						
								<router-link class="btn btn-default" to="/admin/orders/list" v-if="$route.query.type =='pay'">
									Pendentes
					    	</router-link>	  							

					    	<router-link class="btn btn-danger" to="/admin/orders/list/rejected" >
									Rejeitados
					    	</router-link>


					    	<button class="btn btn-primary pull-right" @click.prevent="atualizar()">Atualizar <i class="fa fa-refresh"></i></button>

					    	<div class="col-sm-3">
                                    <h4>expand-right</h4>
                                    <button class="ladda-button btn btn-primary" data-style="expand-right"><span class="ladda-label">Submit</span><span class="ladda-spinner"></span></button>
                                </div>
						 </div>
					</div>					

				</div>
	</div>
</template>

<script>
export default{
	// props:{
	// 	query:{
	// 		required: true,
	// 		type: Object
	// 	}
	// },
	data(){
		return{
			// query: ''
		}
	},
	methods: {
		atualizar(){
			console.log(this.$route.query)

			this.$emit('fetchAllOrders');
		}
	},
	mounted(){
		var l = $( '.ladda-button-demo' ).ladda();
		l.ladda( 'start' );

		setTimeout(function()
              l.ladda('stop');
          },2000)
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