@csrf
<label>Nome<input data-cy="product-name" name="name" value="{{ old('name',$product->name ?? '') }}" required></label>
<label>Categoria<select data-cy="product-category" name="category" required><option value="">Selecione</option>@foreach(['Bolos','Doces','Salgados','Bebidas'] as $category)<option value="{{ $category }}" @selected(old('category',$product->category ?? '') === $category)>{{ $category }}</option>@endforeach</select></label>
<label>Descrição<textarea data-cy="product-description" name="description">{{ old('description',$product->description ?? '') }}</textarea></label>
<label>Preço<input type="number" step="0.01" min="0" data-cy="product-price" name="price" value="{{ old('price',$product->price ?? '') }}" required></label>
<label>Estoque atual<input type="number" min="0" data-cy="product-stock" name="stock" value="{{ old('stock',$product->stock ?? 0) }}" required></label>
<label>Estoque mínimo<input type="number" min="0" data-cy="product-minimum_stock" name="minimum_stock" value="{{ old('minimum_stock',$product->minimum_stock ?? 5) }}" required></label>
<label><input type="checkbox" data-cy="product-active" name="active" value="1" @checked(old('active',$product->active ?? true))> Produto ativo</label>
<div class="actions"><button data-cy="product-save" type="submit">Salvar</button><a class="btn secondary" href="{{ route('produtos.index') }}">Cancelar</a></div>
