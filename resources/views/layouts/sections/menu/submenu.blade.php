@php
use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    @if (isset($submenu->menuHeader))
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __($submenu->menuHeader) }}</span>
      </li>
    @else
    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = 'active open';
      $currentRouteName = Route::currentRouteName() ?? '';

      if (isset($submenu->submenu)) {
        if (is_array($submenu->slug)) {
          foreach($submenu->slug as $slug){
            if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                $activeClass = $active;
                break;
            }
          }
        } else {
          if (str_contains($currentRouteName, $submenu->slug) && strpos($currentRouteName, $submenu->slug) === 0) {
            $activeClass = $active;
          }
        }
      } else {
        if (isset($submenu->slug)) {
          if (is_array($submenu->slug)) {
            foreach($submenu->slug as $slug){
              if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                $activeClass = 'active';
                break;
              }
            }
          } else {
            $baseSlug = str_replace('.index', '', $submenu->slug);
            if ($currentRouteName === $submenu->slug || ($baseSlug && (str_contains($currentRouteName, $baseSlug . '.') || $currentRouteName === $baseSlug))) {
              $activeClass = 'active';
            }
          }
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
    @endforeach
  @endif
</ul>
