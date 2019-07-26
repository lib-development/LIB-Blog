<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search;

use App\Models\Advert;
use App\Models\BlogContent;

class SearchController extends Controller
{
    public function viewSearchResult(Search $request){
        $request_data = $request->all();
        $searchQueryString = $request_data['search'];
        $pageQueryString = isset($request_data['page']) ? $request_data['page'] : 0;
        $baseQuantity = 10;
        $skipQueryString = $pageQueryString * $baseQuantity;
        $search = search_query_constructor((array_filter(explode("-", trim(strip_tags(strtolower($searchQueryString)))))), "lower(title)", true);
        $blog_contents = BlogContent::whereRaw($search)
            ->where('status','1')
            ->orderby('publish_date','desc')
            ->skip($skipQueryString)
            ->limit($baseQuantity)
            ->get();

        $search = $request_data['search'];
        $sidebar = Advert::where('type','0')->orderby('order','asc')->get();
        $inbtw = Advert::where('type','1')->orderby('order','asc')->get();
        $background = Advert::where('type','2')->orderby('order','asc')->get();
        $fp = Advert::where('type','3')->orderby('order','asc')->get();
        $inbtw = $inbtw ? $inbtw->toArray() : [];
        $meta_author = 'LInda Ikeji Admin';
        $meta_title = "Search Result: ".utf8_decode($search);
        $meta_description = "Search results of ".$search;
        $meta_url = url()->current();
        $meta_image = "";
        $meta_time = "";

        $nextPage = $pageQueryString == 0 ? 1 : $pageQueryString + 1;
        $backPage = $pageQueryString <= 0 ? 0 : $pageQueryString - 1;
        $nextSearch = '/search/result?search='. $searchQueryString .'&page=' . $nextPage;
        $backSearch = '/search/result?search='. $searchQueryString .'&page=' . $backPage;
        return view('pages.search')->with(compact('meta_url','meta_time','blog_contents','search','sidebar','inbtw','background','fp','meta_title','meta_description','meta_image','meta_author', 'nextSearch', 'backSearch'));
    }
}
