<div class="menu-wrapper">
    <a href="#menu" class="menu-link"> Menu <i class="fas fa-caret-down right" aria-hidden="true"></i>
    </a>
    <nav id="menu" role="navigation">
        <div class="menu">
            <ul class="menu">
                @foreach (allCategories() as $category)
                <li class="{{ $category->name === 'News' ? 'current-menu-item' : '' }}">
                    <a href="{{ url('/categories/') . $category->id }}">{{ $category->name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
