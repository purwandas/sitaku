<?php

namespace App\Components\Filters;

class ProductUnitFilter extends QueryFilters
{
	public function product_unit($value)
	{
		return is_array($value) ? $this->builder->whereIn('product_units.conversion', $value) : $this->builder->where('product_units.conversion', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function conversion($value)
	{
		return is_array($value) ? $this->builder->whereIn('product_units.conversion', $value) : $this->builder->where('product_units.conversion', $value);
	}
	public function _conversion($value)
	{
		return $this->builder->where('product_units.conversion', 'like', '%'.$value.'%');
	}

	public function price($value)
	{
		return is_array($value) ? $this->builder->whereIn('product_units.price', $value) : $this->builder->where('product_units.price', $value);
	}
	public function _price($value)
	{
		return $this->builder->where('product_units.price', 'like', '%'.$value.'%');
	}

	public function product_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('products.id', $value) : $this->builder->where('product_units.id', $value);
	}

	public function unit_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('units.id', $value) : $this->builder->where('product_units.id', $value);
	}

}