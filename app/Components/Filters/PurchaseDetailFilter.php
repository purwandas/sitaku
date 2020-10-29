<?php

namespace App\Components\Filters;

use Carbon\Carbon;

class PurchaseDetailFilter extends QueryFilters
{
	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function qty($value)
	{
		return is_array($value) ? $this->builder->whereIn('qty', $value) : $this->builder->where('qty', $value);
	}

	public function total($value)
	{
		return is_array($value) ? $this->builder->whereIn('total', $value) : $this->builder->where('total', $value);
	}

	public function price($value)
	{
		return is_array($value) ? $this->builder->whereIn('price', $value) : $this->builder->where('price', $value);
	}

	public function purchase_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('purchase_id', $value) : $this->builder->where('purchase_id', $value);
	}

	public function unit_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('unit_id', $value) : $this->builder->where('unit_id', $value);
	}

	public function product_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('product_id', $value) : $this->builder->where('product_id', $value);
	}

}