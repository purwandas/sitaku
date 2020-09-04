<?php

namespace App\Components\Filters;

class CategoryFilter extends QueryFilters
{
	public function category($value)
	{
		return is_array($value) ? $this->builder->whereIn('categories.name', $value) : $this->builder->where('categories.name', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('categories.name', $value) : $this->builder->where('categories.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('categories.name', 'like', '%'.$value.'%');
	}

}