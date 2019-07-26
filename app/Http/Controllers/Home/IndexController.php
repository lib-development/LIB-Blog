<?php

namespace App\Http\Controllers\Home;

use Cache;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\BlogContent;
use App\Models\Advert;
use App\Models\Setting;

class IndexController extends Controller
{
    public function homeViewContent()
    {
        # Generating pure HTML
        $agent = new Agent();

        if (Cache::get('settings')) {
            $setting = Cache::get('settings');
        } else {
            $setting = Setting::where('id', '1')->first();
            Cache::put('settings', $setting, "30");
        }

        //current_data
        if ($agent->isMobile()) {
            $data = Cache::get('mobile_all_data');
        } else {
            $data = Cache::get('all_data');
        }

        $minutes = 60;
        if ($data) {
            $blog_contents = $data;
            if (isset($_GET['page'])) {

                if ($agent->isMobile()) {
                    //show a 150 content for mobile
                    if (Cache::get('mobile_all_data2')) {
                        $blog_contents = Cache::get('mobile_all_data2');
                    } else {
                        $blog_contents = BlogContent::where('status', "1")->orderby('publish_date', 'desc')->paginate(60);
                        Cache::put('mobile_all_data2', $blog_contents, $minutes);
                    }
                } else {
                    if (Cache::get('all_data2')) {
                        $blog_contents = Cache::get('all_data2');
                    } else {
                        $blog_contents = BlogContent::where('status', "1")->orderby('publish_date', 'desc')->paginate(60);
                        Cache::put('all_data2', $blog_contents, $minutes);
                    }
                }
            }
        } else {
            if ($agent->isMobile()) {
                //show a 150 content for mobile
                $blog_contents = BlogContent::where('status', "1")->orderby('publish_date', 'desc')->paginate(60);
                Cache::put('mobile_all_data', $blog_contents, $minutes);
            } else {
                $blog_contents = BlogContent::where('status', "1")->orderby('publish_date', 'desc')->paginate($setting->number_of_post);
                Cache::put('all_data', $blog_contents, $minutes);
            }
        }

        if (Cache::get('sidebar_o')) {
            $sidebar = Cache::get('sidebar_o');
        } else {
            $sidebar = Advert::where('type', '0')->orderby('order', 'asc')->get();
            Cache::put('sidebar_o', $sidebar, "30");
        }
        if (Cache::get('inbtw')) {
            $inbtw = Cache::get('inbtw');
        } else {
            $inbtw = Advert::where('type', '1')->orderby('order', 'asc')->get();
            Cache::put('inbtw', $inbtw, "30");
        }
        if (Cache::get('background')) {
            $background = Cache::get('background');
        } else {
            $background = Advert::where('type', '2')->orderby('order', 'asc')->get();

            Cache::put('background', $background, "30");
        }

        if (Cache::get('fp')) {
            $fp = Cache::get('fp');
        } else {
            $fp = Advert::where('type', '3')->orderby('order', 'asc')->get();
            Cache::put('background', $fp, "30");
        }

        if ($inbtw) {
            $inbtw = $inbtw->toArray();
        } else {
            $inbtw = [];
        }

        //second half of data
        $data2 = Cache::get('side_bar');
        if (Cache::has('side_bar')) {
            $blog_contents2 = $data2;
        } else {
            $blog_contents2 = BlogContent::where('status', 1)->orderby('publish_date', 'desc')->skip(60)->take(50)->get();
            Cache::put('side_bar', $blog_contents2, $minutes);
        }

        $meta_title = $setting->name;
        $meta_description = $setting->description;
        $meta_url = url()->current();
        $meta_image = "";
        $meta_time = "";

        // Get all the advert rolling out
        $keywords = "blogs";
        if ($agent->isMobile()) {
            //show a 150 content for mobile
            return view('mhome')->with(@compact('sidebar', 'keywords', 'meta_time', 'meta_url', 'comments', 'inbtw', 'background', 'cpm', 'fp', 'blog_contents', 'blog_contents2', 'meta_title', 'meta_description', 'meta_image', 'meta_author'));
        } else {
            return view('home')->with(@compact('sidebar', 'keywords', 'meta_time', 'meta_url', 'comments', 'inbtw', 'background', 'cpm', 'fp', 'blog_contents', 'blog_contents2', 'meta_title', 'meta_description', 'meta_image', 'meta_author'));
        }
    }
}
