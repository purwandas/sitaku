<?php

namespace App\Components\Filters;

class TrendMomentFilter extends QueryFilters
{
	public function trend_moment($value)
	{
		return is_array($value) ? $this->builder->whereIn('trend_moments.month_', $value) : $this->builder->where('trend_moments.month_', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function month_($value)
	{
		return is_array($value) ? $this->builder->whereIn('trend_moments.month_', $value) : $this->builder->where('trend_moments.month_', $value);
	}
	public function _month_($value)
	{
		return $this->builder->where('trend_moments.month_', 'like', '%'.$value.'%');
	}

	public function year_($value)
	{
		return is_array($value) ? $this->builder->whereIn('trend_moments.year_', $value) : $this->builder->where('trend_moments.year_', $value);
	}
	public function _year_($value)
	{
		return $this->builder->where('trend_moments.year_', 'like', '%'.$value.'%');
	}

	public function total_sales($value)
	{
		return is_array($value) ? $this->builder->whereIn('trend_moments.total_sales', $value) : $this->builder->where('trend_moments.total_sales', $value);
	}
	public function _total_sales($value)
	{
		return $this->builder->where('trend_moments.total_sales', 'like', '%'.$value.'%');
	}

}