<div class="menu-wrapper">
    <a href="#menu" class="menu-link active"> <i style="cursor: pointer" class="fas fa-bars left" aria-hidden="true"></i>
    </a>
    <nav id="menu" role="navigation">
        <div class="menu">
            <ul class="menu">
                @foreach (allCategories() as $category)
                <li class="{{ $category->name === 'News' ? 'current-menu-item' : '' }}">
                    <a style="text-align: left; padding-left: 10px" href="/{{ $category->slug }}">{{ $category->name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
