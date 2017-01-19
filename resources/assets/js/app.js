require('./bootstrap');

// Vue.component('example', require('./components/Example.vue'));


import VueLocalStorage from 'vue-localstorage'
import 	OrdersList from './components/Orders/OrdersList.vue'
import VueRouter from 'vue-router'
import OrderView from './components/Orders/OrderView.vue'
Vue.use(VueLocalStorage)
Vue.use(VueRouter)


Vue.http.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;

const routes = [
  { path: '/admin/orders/list', query: {} , name: 'orders-list' , component: OrdersList },
  // { path: '/admin/orders/list', component: OrdersList },
  { path: '/admin/orders/view-order/:orderid', component: OrderView }
]

const router = new VueRouter({
  routes,
  mode: 'history'
})

var app = new Vue({
	localStorage: {
		token: ''
	},
	router,
	el: '#app',
	components:{
		OrdersList, OrderView
	},
	mounted(){


	},
	methods: {
		
	}
});


window.convertArrayOfObjectsToCSV = function(args) {
	var result, ctr, keys, columnDelimiter, lineDelimiter, data;

	data = args.data || null;
	if (data == null || !data.length) {
		return null;
	}

	columnDelimiter = args.columnDelimiter || ',';
	lineDelimiter = args.lineDelimiter || '\n';

	keys = Object.keys(data[0]);

	result = '';
	result += keys.join(columnDelimiter);
	result += lineDelimiter;

	data.forEach(function(item) {
		ctr = 0;
		keys.forEach(function(key) {
			if (ctr > 0) result += columnDelimiter;

			result += item[key];
			ctr++;
		});
		result += lineDelimiter;
	});

	return result;
}

window.downloadCSV = function(args, arr) {
	var data, filename, link;

	var csv = convertArrayOfObjectsToCSV({
		data: arr
	});
	if (csv == null) return;

	filename = args.filename || 'export.csv';

	if (!csv.match(/^data:text\/csv/i)) {
		csv = 'data:text/csv;charset=utf-8,' + csv;
	}
	data = encodeURI(csv);

	link = document.createElement('a');
	link.setAttribute('href', data);
	link.setAttribute('download', filename);
	link.click();
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};