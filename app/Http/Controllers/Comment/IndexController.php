<?php

namespace App\Http\Controllers\Comment;

use Cache;
use App\Models\Comment;
use App\Models\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function saveComment(Request $request)
    {
        $requestData = $request->all();
        if (empty($requestData)) {
            return response('Linda Ikeji\'s Blog.', 200);
        }
        $requestData['status'] = 0;
        $requestData['comments'] = utf8_encode(strip_tags($requestData['comment']));
        $requestData['name'] = (!empty($requestData['full_name']) ? $requestData['full_name'] : "Anonymous");
        $requestData['blog_content_id'] = encrypt_decrypt('decrypt', $requestData['blog_id']);
        $requestData['parent_id'] = (!empty($requestData['parent_id']) ? $requestData['parent_id'] : null);

        $comments = Comment::where('blog_content_id', $requestData['blog_content_id'])->where('name', $requestData['name'])->pluck('comments', 'id');

        $comments = $comments ? $comments->toArray() : [];

        if (in_array($requestData['comments'], $comments)) {
            return response('success', 200);
            exit;
        }

        $text = strip_tags($requestData['comment']);
        $matches = array();

        // returns all results in array $matches
        preg_match_all('/[0-9]{3}[\-][0-9]{6}|[0-9]{3}[\s][0-9]{6}|[0-9]{3}[\s][0-9]{3}[\s][0-9]{4}|[0-9]{9}|[0-9]{3}[\-][0-9]{3}[\-][0-9]{4}/', $text, $matches);
        $matches = $matches[0];

        if (count($matches) > 0) {
            return response('success', 200);
        }
        $string = strip_tags($requestData['comment']);

        if (preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match) > 0) {
            return response(null, 400);
        }

        if (count($match[0]) > 0) {
            return response('success', 200);
        }
        $settings  = Cache::get('settings');
        if (!$settings) {
            $settings = Setting::where('id', '1')->first();
            Cache::put('settings', $settings, 120);
        }
        $comments = $settings->comments;
        $emails = $settings->emails;
        $array_words = explode(',', $comments);
        $array_emails = explode(',', $emails);

        foreach ($array_words as $word) {
            if (strpos(strtolower($requestData['comments']), $word) !== false) {
                return response('success', 200);
                exit;
            }
        }

        foreach ($array_emails as $email) {
            if (strpos(strtolower($requestData['email']), strtolower($email)) !== false) {
                return response('success', 200);
                exit;
            }
        }

        //check if the following
        Comment::create($requestData);
        return response('success', 200);
    }

    public function voteLikes(Request $request){
        //get the current total likes
        $requestData = $request->all();
        if(empty($requestData)){
            return response('Linda Ikeji\'s Blog.', 200);
        }
        $comment = Comment::where('id',$requestData['id'])->first();
        if($requestData['remove'] == 1){
            $total = (int)$comment->likes - 1;
        }else{
            $total = 1 + (int)$comment->likes;
        }
        Comment::where('id',$requestData['id'])->update(['likes' => $total]);
        if($requestData['remove'] == 1) {
            return "removed";
        }else{
            return response('success', 200);
        }
    }

    public function voteDislikes(Request $request){
        // get the current total likes
        $requestData = $request->all();
        if(empty($requestData)){
            return response('Linda Ikeji\'s Blog.', 200);
        }

        $comment = Comment::where('id',$requestData['id'])->first();

        if($requestData['remove'] == 1){
            $total = (int)$comment->dislikes - 1;
        } else{
            $total = 1 + (int)$comment->dislikes;
        }

        Comment::where('id',$requestData['id'])->update(['dislikes' => $total]);

        if($requestData['remove'] == 1) {
            return "removed";
        }else{
            return response('success', 200);
        }
    }
}
