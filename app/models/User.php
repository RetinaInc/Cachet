<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Items which can be mass assigned.
     * @var array
     */
    protected $fillable = ['username'];

    /**
     * Hash any password being inserted by default
     *
     * @param string @password
     * @return void
     */
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }

    public function getGravatarAttribute($size = 200) {
        return 'https://www.gravatar.com/avatar/' . md5($this->email) . '?size=' . $size;
    }

}
