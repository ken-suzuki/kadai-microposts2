<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId) { // 既にフォローしているかの確認 

        $exist = $this->is_following($userId); // 自分自身ではないかの確認 
        
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
    
            // 既にフォローしていれば何もしない
    
            return false;
    
        } else {

        // 未フォローであればフォローする

        $this->followings()->attach($userId);

        return true;

    }
}

    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favoritings()
    {
        // 第一引数にUserがお気に入りするMicropostモデルを指定。第二引数に中間テーブル、第三引数に自分の id を示すカラム名、第四引数に中間テーブルに保存されている関係先の id を示すカラム名を指定。
        return $this->belongsToMany(Micropost::class, 'user_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function like($micropostId)
    {
        // 既にお気に入りしているかの確認 
        $exist = $this->is_liking($micropostId);
    
        if ($exist) {
    
            // 既にお気に入りしていれば何もしない
    
            return false;
    
        } else {
    
            // お気に入りしていなければ、お気に入りに追加する
    
            $this->favoritings()->attach($micropostId);
    
            return true;
    
        }
    }
    
    public function unlike($micropostId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_liking($micropostId);

        if ($exist) {
            // 既にお気に入りしていれば、お気に入りを解除
            $this->favoritings()->detach($micropostId);
            return true;
        } else {
            // お気に入りしていなければ何もしない
            return false;
        }
    }
    
    public function is_liking($micropostId) {
        return $this->favoritings()->where('micropost_id', $micropostId)->exists();
    }
}