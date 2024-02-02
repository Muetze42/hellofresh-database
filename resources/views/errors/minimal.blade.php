<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ $__env->yieldContent('code') .' - ' . __($__env->yieldContent('title')) }}</title>
        <link href="{{ asset('css/error-pages.css') }}" rel="stylesheet">
    </head>
    <body class="antialiased font-sans">
        <div class="md:flex min-h-screen">
            <div class="w-full md:w-1/2 bg-white flex items-center justify-center">
                <div class="max-w-sm m-8">
                    <div class="text-black text-5xl md:text-15xl font-black">
                        @yield('code', __('Oh no'))
                    </div>

                    <div class="w-16 h-1 bg-purple-light my-3 md:my-6"></div>

                    <p class="text-grey-darker text-2xl md:text-3xl font-light mb-8 leading-normal">
                        {!! nl2br(e(__($__env->yieldContent('message')))) !!}
                    </p>
                    @if($__env->yieldContent('code') == 419)
                        <a href="{{ url()->current() }}">
                            <button class="bg-transparent text-grey-darkest font-bold uppercase tracking-wide py-3 px-6 border-2 border-grey-light hover:border-grey rounded-lg">
                                {{ __('Reload page') }}
                            </button>
                        </a>
                    @elseif($__env->yieldContent('code') != 503)
                        <a href="{{ app('router')->has('home') ? route('home') : url('/') }}">
                            <button class="bg-transparent text-grey-darkest font-bold uppercase tracking-wide py-3 px-6 border-2 border-grey-light hover:border-grey rounded-lg">
                                {{ __('Go Home') }}
                            </button>
                        </a>
                    @endif
                </div>
            </div>
            <div class="relative pb-full md:flex md:pb-0 md:min-h-screen w-full md:w-1/2">
                @php
                    $errorImages = [
                        '401' => '403.svg',
                        '403' => '403.svg',
                        '404' => '404.svg',
                        '500' => '503.svg',
                    ];

                    $errorImage = $errorImages[$__env->yieldContent('code')] ?? '404.svg';
                @endphp
                <div style="background-image: url({{ asset('assets/' . $errorImage) }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
            </div>
        </div>
        @if($__env->yieldContent('code') == 503)
            <script>
                setInterval(function() {
                    fetch('/api')
                        .then((response) => {
                            if (response.status !== 503) {
                                let iframe = window.parent.document.getElementsByTagName('iframe')
                                let parent = null
                                if (iframe[0]) {
                                    parent = iframe[0].parentElement
                                }
                                if (iframe && parent) {
                                    let parent = iframe[0].parentElement
                                    let app = window.parent.document.getElementsByTagName('body')
                                    app[0].style.overflowY = 'visible';
                                    window.parent.location.href = '{{ url()->full() }}';
                                    parent.parentNode.removeChild(parent)
                                } else {
                                    location.reload()
                                }
                            }
                        })
                }, 1000);
            </script>
        @endif
    </body>
</html>
