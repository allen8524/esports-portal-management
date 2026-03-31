@php
  $role = strtolower((string)($role ?? $player->role ?? ''));
  $valid = ['top','jungle','mid','adc','support'];
  $iconName = in_array($role, $valid) ? $role : 'default';
  $iconRel  = "img/roles/{$iconName}.svg";
@endphp

@if($role)
  <span class="chip role-{{ $role }}" title="포지션">
    <img src="{{ asset($iconRel) }}" alt="{{ $role }} icon"
         class="icon-32" width="32" height="32">
    {{ strtoupper($role) }}
  </span>
@endif
