@php
use Illuminate\Support\Facades\Route;

$currentRouteName = Route::currentRouteName() ?? '';

// Determine which leaf submenu item is the single best match
$bestActiveIndex = null;
$highestScore = 0;

if (isset($menu)) {
    foreach ($menu as $index => $item) {
        if (isset($item->menuHeader) || !isset($item->slug) || isset($item->submenu) || ! \App\Helpers\MenuHelper::isItemVisible($item)) {
            continue;
        }

        $slugs = is_array($item->slug) ? $item->slug : [$item->slug];
        foreach ($slugs as $slug) {
            $score = 0;
            if ($currentRouteName === $slug) {
                $score = 10000 + strlen($slug);
            } else {
                $prefix = str_ends_with($slug, '.index') ? substr($slug, 0, -5) : $slug . '.';
                if (str_starts_with($currentRouteName, $prefix)) {
                    $score = 1000 + strlen($prefix);
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestActiveIndex = $index;
            }
        }
    }
}
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)
    @if (\App\Helpers\MenuHelper::isItemVisible($submenu))

    @if (isset($submenu->menuHeader))
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __($submenu->menuHeader) }}</span>
      </li>
    @else
    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = 'active open';

      if (isset($submenu->submenu)) {
        $slugs = is_array($submenu->slug) ? $submenu->slug : (isset($submenu->slug) ? [$submenu->slug] : []);
        foreach ($slugs as $slug) {
          if (str_starts_with($currentRouteName, $slug . '.') || $currentRouteName === $slug) {
            $activeClass = $active;
            break;
          }
        }
      } else {
        if ($bestActiveIndex === $loop->index) {
          $activeClass = 'active';
        }
      }
    @endphp

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
          <i class="{{ $submenu->icon }}"></i>
          @endif
          <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          @isset($submenu->badge)
            <div class="badge rounded-pill bg-{{ $submenu->badge[0] }} text-uppercase ms-auto">{{ $submenu->badge[1] }}</div>
          @endisset
        </a>

        {{-- submenu --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu',['menu' => $submenu->submenu])
        @endif
      </li>
    @endif
    @endif
    @endforeach
  @endif
</ul>
