<template v-for="product in bill">
	<tr class="product-row">
		<td class="product-row-data">@{{ product.quantity }}</td>
		<td class="product-row-data">@{{ product.name }}</td>
		<td class="product-row-data">@{{ product.price / 100 }}</td>
	</tr>
</template>