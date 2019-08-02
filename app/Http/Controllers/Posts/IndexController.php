<?php

namespace App\Http\Controllers\Posts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function blogContent($year = null, $month = null, $slug = null)
    {
        $blog_content = "";
        dd($blog_content_view);
        $slug = explode(".", $slug);
        $slug = $slug[0];
        if (isset($slug)) {
            //update count on a post
            if (Cache::get($slug)) {
                $blog_content = Cache::get($slug);
            } else {
                $blog_content = BlogContent::where('slug', $slug)->where('year', $year)->where('month', $month)->first();
                Cache::put($slug, $blog_content, "120");
            }

            if ($blog_content) {

                if ($blog_content->status != "1") {
                    return back();
                }
            } else {
                return redirect()->to('/');
            }

            $s_c = [];

            $sub_commments = Comment::where('blog_content_id', $blog_content->id)->where('status', '1')->where('parent_id', "!=", $blog_content->id)->get();

            if ($sub_commments->count() > 0) {
                $sub_commments = $sub_commments->toArray();

                foreach ($sub_commments as $sub_commment) {
                    $s_c[$sub_commment['parent_id']][] = $sub_commment;
                }
            }

            $meta_title = utf8_decode($blog_content->title);
            $keywords = explode(" ", $meta_title);
            $keywords = implode(",", $keywords);
            $meta_description = substr(strip_tags($blog_content->content), 0, 200);
            $meta_url = url()->current();
            $meta_time = Carbon::parse($blog_content->publish_date)->toW3cString();

            $content = $blog_content->content;
            $str = preg_replace('/<img(.*)>/i', '', $content, 1);
            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);

            $img_src = (isset($image['src']) ? $image['src'] : "");

            $meta_image = $img_src;

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

            $sub_data = (int) count($sidebar);

            if ($sub_data >= 5) {
                $sub_data = 4;
            }

            if (Cache::get("blog_content2")) {
                $blog_contents2 = Cache::get("blog_content2");
            } else {
                $blog_contents2 = BlogContent::where('status', '1')->orderby('created_at', 'desc')->skip(50)->take(20 - $sub_data)->get();
                Cache::put("blog_content2", $blog_contents2, "120");
            }

            // Content with the largest amount of in the last few days
            $start = Carbon::now()->subDay(3)->format('Y-m-d') . " 00:00:00";
            $end = Carbon::now()->format('Y-m-d') . " 23:59:59";;

            if (Cache::get("blog_content_view")) {
                $blog_content_view = Cache::get("blog_content_view");
            } else {
                $blog_content_view = BlogContent::whereBetween('publish_date', [$start, $end])->orderby('views', 'desc')->paginate(12);
                Cache::put("blog_content_view", $blog_content_view, "120");
            }
            dd($blog_content_view);
            return view('post')->with(@compact('sidebar', "keywords", 's_c', 'meta_url', 'meta_time', 'comments', 'blog_content_view', 'blog_contents2', 'inbtw', 'background', 'fp', 'blog_content', 'meta_title', 'meta_description', 'meta_image', 'meta_author'));
        } else {
            return redirect()->to('/');
        }
    }
}
