<template v-for="product in inventory">
	<tr class="product-row">
		<td class="product-row-data">@{{ product.quantity }}</td>
		<td class="product-row-data">@{{ product.name }}</td>
		<td class="product-row-data">@{{ product.price }}</td>
	</tr>
</template>