<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Advert;
use App\Models\BlogContent;
use App\Models\Category;

use Cache;

class CategoriesController extends Controller
{
    public function viewCategories (Request $request, $param)
    {
        if (is_numeric($param)) {
            $category = Category::where('id', $param)->first();
        } else {
            $category = Category::where('slug', $param)->first();
        }

        if(!$category) {
            return redirect('/');
        }

        $request_data = $request->all();
        $pageQueryString = isset($request_data['page']) ? $request_data['page'] : 0;
        $baseQuantity = 100;
        $skipQueryString = $pageQueryString * $baseQuantity;
        $sidebar = Advert::where('type','0')->orderby('order','asc')->get();
        $inbtw = Advert::where('type','1')->orderby('order','asc')->get();
        $background = Advert::where('type','2')->orderby('order','asc')->get();
        $fp = Advert::where('type','3')->orderby('order','asc')->get();
        $inbtw = $inbtw ? $inbtw->toArray() : [];
        $meta_author = 'LInda Ikeji Admin';
        $meta_title = ucfirst($category->name) . " - Linda Ikeji's Blog";
        $meta_description = '<p style="font-size: 15px;background:#8e0f2c;padding: 10px;color: #fff;">Total of <strong>'.$category->blogPosts()->count() . "</strong> posts found.</p>";
        $meta_url = url()->current();
        $meta_image = "";
        $meta_time = "";
        $categoryPosts = BlogContent::where('category_id', $category->id)
            ->where('status','1')
            ->orderby('publish_date','desc')
            ->skip($skipQueryString)
            ->limit($baseQuantity)
            ->get();

        // if(Cache::has('side_bar')){
        //     $blog_contents2 = Cache::get('side_bar');
        // } else {
            $blog_contents2 = BlogContent::where('status', 1)
                ->orderby('publish_date', 'desc')
                ->skip($skipQueryString * 2)
                ->take(50)->get();
        // }

        Cache::put('side_bar', $blog_contents2, 60);

        $nextPage = $pageQueryString == 0 ? 1 : $pageQueryString + 1;
        $backPage = $pageQueryString <= 0 ? 0 : $pageQueryString - 1;
        $nextSearch = '?page=' . $nextPage;
        $backSearch = '?page=' . $backPage;

        return view('view_category')->with(compact('blog_contents2', 'meta_url','meta_time','category', 'categoryPosts','sidebar','inbtw','background','fp','meta_title','meta_description','meta_image','meta_author', 'nextSearch', 'backSearch'));
    }
}
