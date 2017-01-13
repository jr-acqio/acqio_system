require('./bootstrap');

// Vue.component('example', require('./components/Example.vue'));


import VueLocalStorage from 'vue-localstorage'
import 	Orders from './components/Orders.vue'
Vue.use(VueLocalStorage)


Vue.http.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;

const app = new Vue({
	localStorage: {
		token: ''
	},
	el: '#app',
	components:{
		Orders,
	},
	mounted(){

	
	},
	methods: {
		
	}
});