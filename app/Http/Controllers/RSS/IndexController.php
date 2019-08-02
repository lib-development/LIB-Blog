<?php

namespace App\Http\Controllers\RSS;
use App;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
        $feed = App::make("feed");

        $feed->setCache(20, 'laravelFeedKey');

        // check if there is cached feed and build new only if is not
        if (!$feed->isCached()) {
            // creating rss feed with our most recent 20 posts
            $posts = DB::table('blog_content')->where('status', "1")->orderBy('created_at', 'desc')->limit(50)->get();

            // set your feed's title, description, link, pubdate and language
            $feed->title = "Linda Ikeji's Blog";
            $feed->description = "Welcome to Linda Ikeji's Blog";
            $feed->logo = 'http://www.lindaikejisblog.com/img/favicon.png';
            $feed->link = url('feed');
            $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
            $feed->pubdate = Carbon::parse($posts[0]->created_at)->toRfc822String();
            $feed->lang = 'en';
            $feed->setShortening(true); // true or false
            $feed->setTextLimit(100); // maximum length of description text

            foreach ($posts as $post) {
                $url = "http://www.lindaikejisblog.com/" . $post->year . "/" . $post->month . "/" . $post->slug . ".html";
                // set item's title, author, url, pubdate, description, content, enclosure (optional)*
                $content = $post->content;
                $content = strip_tags($content, "<img>");
                $content = str_replace('alexis', 'www', $content) . 'The post <a  href="' . $url . '">' . $post->title . '</a> appeared first on <a href="https://www.lindaikejisblog.com">Linda Ikeji Blog</a>.';
                $title = strip_tags($post->title);
                $content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content);

                $badchar = array(
                    // control characters
                    chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8), chr(9), chr(10),
                    chr(11), chr(12), chr(13), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19), chr(20),
                    chr(21), chr(22), chr(23), chr(24), chr(25), chr(26), chr(27), chr(28), chr(29), chr(30),
                    chr(31),
                    // non-printing characters
                    chr(127)
                );
                $content = str_replace($badchar, '', $content);
                $content = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $content);
                $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
                $content = str_replace("\x80\x93 ", "", $content);
                $content = str_replace("\x80\x99 ", "", $content);

                //reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
                $enclose = ['url' => $this->getImage($post->content), 'type' => 'image/jpeg'];

                $feed->add($title, $post->author, $url, Carbon::parse($post->created_at)->toRfc822String(), $content, $content, $enclose);
            }
        }

        // first param is the feed format
        // optional: second param is cache duration (value of 0 turns off caching)
        // optional: you can set custom cache key with 3rd param as string
        return $feed->render('atom', 0);
    }

    function special_chars($str)
    {
        $str = htmlentities($str, ENT_COMPAT, 'iso-8859-1');
        $str = preg_replace('/&(.)(acute|cedil|circ|lig|grave|ring|tilde|uml);/', "$1", $str);
        return $str;
    }

    function getImage($content)
    {
        $str = preg_replace('/<img(.*)>/i', '', $content, 1);
        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
        $img_src = (isset($image['src']) ? $image['src'] : "");


        return (isset($image['src']) ? $image['src'] : "https://dummyimage.com/1x1/000/fff");
    }

    function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }
}
