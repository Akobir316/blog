<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, Sluggable;
    protected $fillable = ['title','description', 'content','category_id','thumbnail'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
     public function sluggable(): array
     {
         return [
             'slug' => [
                 'source' => 'title'
             ]
         ];
     }
    public static function uploadImage(Request $request, $image = null)
    {
        if ($request->hasFile('thumbnail')) {
            if ($image) {
                Storage::delete($image);
            }
            $folder = date('Y-m-d');
            return $request->file('thumbnail')->store("images/{$folder}");
        }
        return null;
    }

    public function getImage()
    {
        if (!$this->thumbnail) {
            return asset("assets/admin/img/no-image.png");
        }
        return asset("uploads/{$this->thumbnail}");
    }

    public function getPostDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d F, Y');
    }

    public static function setRecentlyViewed($id)
    {

        $recentlyViewed = self::getAllRecentlyViewed();
        if(!$recentlyViewed){
            Cookie::queue('recentlyViewed', $id, 120);
        }else{
            $recentlyViewed = explode('.', $recentlyViewed);
                $recentlyViewed[] = $id;
                $recentlyViewed = implode('.', $recentlyViewed);
                Cookie::queue('recentlyViewed', $recentlyViewed,120);
        }
    }
    public function getRecentlyViewed()
    {
        if(!empty(Cookie::get('recentlyViewed'))){
            $recentlyViewed = Cookie::get('recentlyViewed');
            $recentlyViewed = explode('.', $recentlyViewed);
            return array_slice($recentlyViewed, -3);
        }
        return false;
    }

    public static function  getAllRecentlyViewed()
    {
        if(!empty(Cookie::get('recentlyViewed'))){
            return Cookie::get('recentlyViewed');
        }
        return false;
    }
}
