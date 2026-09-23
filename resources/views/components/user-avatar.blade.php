@props(['user', 'alt' => null, 'showPhoto' => true])

@php($photoUrl = $showPhoto ? $user?->profilePhotoUrl() : null)
<span {{ $attributes->merge(['class' => 'user-avatar']) }}>
    @if($photoUrl)
        <img src="{{ $photoUrl }}" alt="{{ $alt ?: 'Foto profil ' . $user->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
        <span hidden aria-label="{{ $alt ?: 'Avatar ' . ($user?->name ?? '') }}">{{ $user?->initials() ?? '?' }}</span>
    @else
        <span aria-label="{{ $alt ?: 'Avatar ' . ($user?->name ?? '') }}">{{ $user?->initials() ?? '?' }}</span>
    @endif
</span>
