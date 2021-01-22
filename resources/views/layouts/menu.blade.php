{{-- BEGIN CONFIG --}}
@php
    $icon1 = 'mdi mdi-monitor';
    $icon2 = 'mdi mdi-cards-playing-outline';

    $configIcon = [];
    $configIcon['frequentlyAskedQuestion'] = "mdi mdi-comment-question";

    $configSeparator = [];
    $configSeparator['country'] = "MASTER DATA";

    $configLabel = [];
    $configLabel['videoSpace'] = "Video Room";
@endphp
{{-- END CONFIG --}}

<!-- Left Sidenav -->
<div class="left-sidenav">
    
    <ul class="metismenu left-sidenav-menu" id="side-nav">

        {{-- <li class="menu-title">Main</li> --}}

        @foreach(MenuBuilderHelper::getMenu() as $key => $menu)
            @php
                $camel = Str::camel($menu['label']);

                var_dump($menu);

                if (($menu['key'] == 'role' || $menu['key'] == 'user') && (\Auth::user()->role_id == 2)) {
                    continue;
                }

                if ( count($configIcon) > 0 && array_key_exists($camel, $configIcon) ) {
                    $icon = $configIcon[$camel];
                } else {
                    if(array_key_exists("sub-menu", $menu)) {
                        $icon = $icon2;
                    } else {
                        $icon = $icon1;
                    }
                }

                if ( count($configSeparator) > 0 && array_key_exists($camel, $configSeparator) ) {
                    echo createMenuSeparator($configSeparator[$camel]);
                }

                if ( count($configLabel) > 0 && array_key_exists($camel, $configLabel) ) {
                    $label = $configLabel[$camel];
                }

            @endphp
            @if(array_key_exists("sub-menu", $menu))
                <li class="{{array_key_exists('url', $menu) && \Route::currentRouteName() == $menu['url'] ? 'active' : ''}}">
                    <a href="javascript: void(0);"><i class="{{$icon}}"></i><span>{{getMenuLabel($configLabel, $menu['label'])}}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                    <ul class="nav-second-level" aria-expanded="false">
                        @foreach($menu['sub-menu'] as $submenu)
                            @if(array_key_exists("sub-menu", $submenu))
                                <li class="{{array_key_exists('url', $submenu) && \Route::currentRouteName() == $submenu['url'] ? 'active' : ''}}">
                                    <a href="javascript: void(0);">{{$submenu['label']}} <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        @foreach($submenu['sub-menu'] as $submenu2)
                                            @php 
                                                displayMenu($submenu2, $configLabel) 
                                            @endphp
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                @php 
                                    displayMenu($submenu, $configLabel) 
                                @endphp
                            @endif        
                        @endforeach
                    </ul>
                </li>
            @else
                @php 
                    displayMenu($menu, $configLabel, $icon)
                @endphp
            @endif
        @endforeach
    </ul>
</div>
<!-- end left-sidenav -->