<template v-for="product in bill">
	<tr class="product-row" v-cloak>
		<td class="product-row-data">@{{ product.quantity }}</td>
		<td class="product-row-data">@{{ product.name }}</td>
		<td class="product-row-data">$@{{ (product.price / 100).toFixed(2) }}</td>
		<td class="product-row-data"><span class="glyphicon glyphicon-minus-sign" v-on:click="subtractProduct(product)"></span></td>
	</tr>
</template>