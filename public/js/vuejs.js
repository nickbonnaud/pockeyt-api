Vue.component('products', {
	template: '#products-template',

	data: function() {
		return {
			inventory: []
		};
	},

	created: function() {
		$.getJSON('products/inventory/{{ $business->id }}', function(data) {
			console.log(data);
		})
	}

});

new Vue({
	el: '#inventory'
});