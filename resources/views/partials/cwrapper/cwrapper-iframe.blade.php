{{-- Content wrapper for IFrame mode --}}

<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">
        <div class="nav-item dropdown">
            @if(config('adminlte.layout_iframe.buttons.close.active'))
                @if(config('adminlte.layout_iframe.buttons.close-all.active') || config('adminlte.layout_iframe.buttons.close-all-other.active'))
                    <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{ config('adminlte.layout_iframe.buttons.close.caption') }}</a>
                @else
                    <a class="nav-link bg-danger" href="#" data-widget="iframe-close">Close</a>
                @endif
            @endif
            @if(config('adminlte.layout_iframe.buttons.close-all.active') || config('adminlte.layout_iframe.buttons.close-all-other.active'))
                <div class="dropdown-menu mt-0">
                    @if(config('adminlte.layout_iframe.buttons.close-all.active'))
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all">{{ config('adminlte.layout_iframe.buttons.close-all.caption') }}</a>
                    @endif
                    @if(config('adminlte.layout_iframe.buttons.close-all-other.active'))
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other">{{ config('adminlte.layout_iframe.buttons.close-all-other.caption') }}</a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Render button left --}}
        @if(config('adminlte.layout_iframe.buttons.scroll-left'))
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft"><i class="fas fa-angle-double-left"></i></a>
        @endif

        <ul class="navbar-nav" role="tablist">
            {{-- Starting a page by default --}}
            @if(!empty(config('adminlte.layout_iframe.url-default.url')))
                <li class="nav-item active" role="presentation">
                    <a class="nav-link active" data-toggle="row" id="tab-index" href="#panel-index" role="tab" aria-controls="panel-index" aria-selected="true">
                        {{ config('adminlte.layout_iframe.url-default.title') ?: 'Dashboard' }}
                    </a>
                </li>
            @endif
        </ul>

        {{-- Render button Right --}}
        @if(config('adminlte.layout_iframe.buttons.scroll-right'))
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright"><i class="fas fa-angle-double-right"></i></a>
        @endif

        {{-- Render button Fullscreen --}}
        @if(config('adminlte.layout_iframe.buttons.fullscreen'))
            <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen"><i class="fas fa-expand"></i></a>
        @endif
    </div>
    <div class="tab-content">
        @if(!empty(config('adminlte.layout_iframe.url-default.url')))
            <div class="tab-pane fade active show" id="panel-index" role="tabpanel" aria-labelledby="tab-index">
                <iframe src=" {{ config('adminlte.layout_iframe.url-default.url') }}" style="height: 671px;"></iframe>
            </div>
        @endif
        <div class="tab-empty">
            <h2 class="display-4">{{ config('adminlte.layout_iframe.captions.no-tab') ?: 'No tab selected!' }}</h2>
        </div>
        <div class="tab-loading">
            <div>
                <h2 class="display-4">{{ config('adminlte.layout_iframe.captions.loading') ?: 'Tab is loading' }} <i class="fa fa-sync fa-spin"></i></h2>
            </div>
        </div>
    </div>
</div>