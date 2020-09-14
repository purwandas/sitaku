<?php

namespace App\Components\Filters;

class ProductFilter extends QueryFilters
{
	public function product($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.name', $value) : $this->builder->where('products.name', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.name', $value) : $this->builder->where('products.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('products.name', 'like', '%'.$value.'%');
	}

	public function stock($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.stock', $value) : $this->builder->where('products.stock', $value);
	}
	public function _stock($value)
	{
		return $this->builder->where('products.stock', 'like', '%'.$value.'%');
	}

	public function buying_price($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.buying_price', $value) : $this->builder->where('products.buying_price', $value);
	}
	public function _buying_price($value)
	{
		return $this->builder->where('products.buying_price', 'like', '%'.$value.'%');
	}

	public function selling_price($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.selling_price', $value) : $this->builder->where('products.selling_price', $value);
	}
	public function _selling_price($value)
	{
		return $this->builder->where('products.selling_price', 'like', '%'.$value.'%');
	}

	public function unit_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('units.id', $value) : $this->builder->where('products.id', $value);
	}

	public function category_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('categories.id', $value) : $this->builder->where('products.id', $value);
	}

	public function production_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('productions.id', $value) : $this->builder->where('products.id', $value);
	}

}