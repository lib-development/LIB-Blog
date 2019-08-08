@extends('layouts.home')

@section('content')
    <div class="main" style="margin-bottom: 10px;">
        @if($fp->count() >0 )
            <div class="da x728">
                @if($fp->count() >0 )
                    @foreach($fp as $front_page)
                        {!! $front_page->content !!}
                    @endforeach
                @endif
            </div>
        @endif

        <div class="board">
            <div class="container con_style">
                <div class="row">
                    <!-- Main Board Starts Here-->
                    <div class="col-md-8 main_board">
                        <!-- Big Story Row Starts Here -->
                            <h2 style="font-size: 25px;background:#8e0f2c;padding: 10px;color: #fff;">{{ $meta_title }}</h2><br/>
                            @if (count($categoryPosts) >= 1)
                                {!! $meta_description !!}<br/>
                            @endif
                        <div class="row">
                            <!-- Big Story Starts Here -->
                            <div class="col-md-12">
                                <?php $i = $j = 0;?>
                                @if(count($categoryPosts) >= 1)
                                    @foreach($categoryPosts as $post)
                                        @if($i != 0 && $i%2 == 0)

                                            @if(count($inbtw) >0 )
                                                <div class="clearfix"></div>
                                                <div class="da x728">
                                                    <div style="height: 20px;"></div>
                                                    @if(isset($inbtw[$j]))
                                                        {!! $inbtw[$j]['content'] !!}
                                                    @endif
                                                </div>
                                                <hr>
                                                <?php $j++;?>
                                                <br>
                                            @endif
                                        @endif

                                            <article class="result">
                                                <a href="javascript:;" class="pic">
                                                    <?php
                                                    preg_match_all('~<img.*?src=["\']+(.*?)["\']+~', $post->content, $urls);
                                                    if(isset($urls[1][0])){
                                                        $img_d = $urls[1][0];
                                                        echo "<img src='".$img_d."' alt='' style='height: 400px;width:100%;'/>";;
                                                    }else{
                                                        preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $post->content, $matches);
                                                        if(isset($matches[1])){
                                                            echo '<div class="videoWrapper">
                                                                        <iframe src="'.$matches[1].'"></iframe></div>';

                                                        }
                                                    }
                                                    ?>
                                                </a>
                                                @if(auth()->check())
                                                    <a href="{{ url('/p/'.$post->year.'/'.$post->month.'/'.$post->slug) }}.html" class="title">{{ utf8_decode($post->title) }}</a>
                                                @else
                                                    <a href="{{ url('/'.$post->year.'/'.$post->month.'/'.$post->slug) }}.html" class="title">{{ utf8_decode($post->title) }}</a>
                                                @endif
                                                <summary class="description" style="word-wrap: break-word;font-size: 12px;">
                                                    <?php
                                                    $content = preg_replace("/<img[^>]+\>/i", "", $post->content);
                                                    echo substr(utf8_decode(strip_tags($content)),0, 579);
                                                    ?>@if(strlen(utf8_decode($content)) > 579)
                                                        ...
                                                    @endif
                                                </summary>
                                                <div class="meta" style="margin-top: 10px;">
                                                    <?php
                                                    $title = $post->title;
                                                    if(strlen($title) > 50){
                                                        $title = substr($title,0,50)."...";
                                                    }

                                                    ?>
                                                    <a href="javascript:;" onclick="shareData('{{ $title }}','{{ (isset($img_d) ? $img_d: "") }}','{{ url('/p/'.$post->year.'/'.$post->month.'/'.$post->slug) }}.html')" class="share_link">
                                                        <img src="{{ url('images/icon_share.png') }}" alt="">
                                                    </a>
                                                    <div class="post_age"> by Linda Ikeji at {{ \Carbon\Carbon::parse($post->publish_date)->format('d/m/Y g:i A') }}</div>
                                                    <div class="divider">|</div>
                                                    {{-- <div class="comments">{{ $post->approved_comments->count() }} Comments</div> --}}
                                                </div>
                                            </article>
                                        <br/>
                                    <?php $i++;?>
                                @endforeach
                                    <div class="pagination-button">
                                        @if (count($categoryPosts) >= 1)
                                            <a href="{{ $backSearch }}">Back</a>
                                            <a href="{{ $nextSearch }}">Next</a>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        No posts available
                                    </div>
                                @endif
                            </div>
                            <!-- Big Story Ends Here -->
                        </div>
                        <!-- Big Story Row Ends Here -->


                    </div>
                    <!-- Main Board Ends Here-->

                    <div class="col-md-4 side_board">
                        <div class="da side_first">
                            @if($sidebar->count() >0 )
                                <div class="da x728">
                                    @if($sidebar->count() >0 )
                                        @foreach($sidebar as $sb)
                                            {!! $sb->content !!}
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
