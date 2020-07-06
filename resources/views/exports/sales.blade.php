<table>
	<thead>
		<tr>
			<th>Folio</th>
			<th>Fecha de venta</th>
			<th>Productos</th>
			<th>Cantidad</th>
			<th>Cliente</th>
			<th>Total de compra</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $item)
		<tr>
			<th>{{$item->folio}}</th>
			<td>{{$item->created_at->format('d/m/Y')}}</td>
			<td>
				@foreach($item->products as $product)
				@if ($loop->last)
				<div>
					{{$product->name}}
				</div>
				@else
				<div>
					{{$product->name}},
				</div>
				@endif
				@endforeach
			</td>
			<td>
				@foreach($item->products as $product)
				@if ($loop->last)
				<div>
					{{$product->pivot->quantity}}
				</div>
				@else
				<div>
					{{$product->pivot->quantity}},
				</div>
				@endif
				@endforeach
			</td>
			<td>{{$item->client}}</td>
			<td>${{$item->total}}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan='5'>

			</td>
			<td><b>Total ${{$sum}}</b></td>
		</tr>
	</tbody>
</table>