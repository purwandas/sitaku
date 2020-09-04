<?php

namespace App;

use App\Components\Filters\QueryFilters;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function rule(){
        return [
            'name' => 'required|string|min:8|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id',
        ];
    
    }

    public function role()
    {
        return $this->belongsTo($this->_role(), 'role_id');
    }

    public static function _role()
    {
        return '\\App\Role';
    }

    public static function toKey()
    {
        $classModel = explode('\\', static::class);
        $string     = end($classModel);
        $kebab      = Str::kebab($string);

        return [
            'class' => $string,
            'route' => $kebab,
            'snake' => Str::snake($string),
            'title' => str_replace('-', ' ', $kebab),
        ];
    }

    public function fillAndValidate($customData = null, $rule = null)
    {
        $rule = $rule ?? static::rule($this);
        $data = $customData ?? request()->all();
        $attributes = method_exists(static::class, 'attributes') ? static::attributes() : [];

        $validatedData = \Validator::make($data, $rule, [], $attributes)->validate();

        return parent::fill($validatedData);
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public static function labelText()
    {
        return ['name'];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
