<?php

namespace App\Http\Controllers\Posts;

use Cache;
use Illuminate\Pagination\Paginator;

use App\Models\Advert;
use App\Models\BlogContent;
use App\Models\Setting;

use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function getPages($id)
    {
        $agent = new \Jenssegers\Agent\Agent();

        Paginator::currentPageResolver(function () use ($id) {
            return $id;
        });

        if (Cache::get('settings')) {
            $setting = Cache::get('settings');
        } else {
            $setting = Setting::where('id', '1')->first();
            Cache::put('settings', $setting, "30");
        }

        //current_data

        $blog_contents = BlogContent::where('status', "1")->orderby('publish_date', 'desc')->paginate(60);
        $sidebar = Advert::where('type', '0')->orderby('order', 'asc')->get();
        $inbtw = Advert::where('type', '1')->orderby('order', 'asc')->get();
        $background = Advert::where('type', '2')->orderby('order', 'asc')->get();
        $fp = Advert::where('type', '3')->orderby('order', 'asc')->get();

        if ($inbtw) {
            $inbtw = $inbtw->toArray();
        } else {
            $inbtw = [];
        }

        $blog_contents2 = BlogContent::where('status', 1)->orderby('publish_date', 'desc')->skip(60)->take(50)->get();
        $meta_title = $setting->name;
        $meta_description = $setting->description;
        $meta_url = url()->current();
        $meta_image = "";
        $meta_time = "";

        if ($agent->isMobile()) {
            //show a 150 content for mobile
            return view('mhome')->with(@compact('sidebar', 'meta_time', 'meta_url', 'comments', 'inbtw', 'background', 'cpm', 'fp', 'blog_contents', 'blog_contents2', 'meta_title', 'meta_description', 'meta_image', 'meta_author'));
        } else {
            return view('home')->with(@compact('sidebar', 'meta_time', 'meta_url', 'comments', 'inbtw', 'background', 'cpm', 'fp', 'blog_contents', 'blog_contents2', 'meta_title', 'meta_description', 'meta_image', 'meta_author'));
        }
    }
}
