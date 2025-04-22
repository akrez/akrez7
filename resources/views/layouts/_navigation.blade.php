<nav class="navbar navbar-dark bg-dark navbar-expand-lg z-1030">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">وبـلاگ فروشـگاهـی اکــرز</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent1"
            aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent1">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a>
                    </li>
                @endauth
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @else
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if (app('ActiveBlog')->has())
    <nav class="navbar navbar-light bg-light navbar-expand-lg z-1030">
        <div class="container">
            <a class="navbar-brand" href="{{ route('payvoices.index') }}">
                {{ app('ActiveBlog')->name() }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent2" aria-controls="navbarSupportedContent2" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent2">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" target="_blank"
                            href="{{ route('blogs.show', ['blog_id' => app('ActiveBlog')->id()]) }}">
                            {{ __('View') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page"
                            href="{{ route('galleries.index', [
                                'gallery_category' => \App\Enums\GalleryCategoryEnum::BLOG_LOGO->value,
                                'short_gallery_type' => \App\Models\Blog::getClassName(),
                                'gallery_id' => app('ActiveBlog')->id(),
                            ]) }}">
                            {{ \App\Enums\GalleryCategoryEnum::BLOG_LOGO->trans() }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('products.index') }}">
                            {{ __('Products') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('colors.index') }}">
                            {{ __('Colors') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('telegram_bots.index') }}">
                            {{ __('TelegramBots') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('contacts.index') }}">
                            {{ __('Contacts') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endif
