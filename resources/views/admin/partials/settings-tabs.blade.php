@php
    $tabs = [
        'admin.settings.general' => [
            'label' => 'General',
            'icon' => 'fas fa-cog',
            'route' => 'admin.settings.general',
        ],
        'admin.settings.store' => [
            'label' => 'Store',
            'icon' => 'fas fa-store',
            'route' => 'admin.settings.store',
        ],
        'admin.settings.payment' => [
            'label' => 'Payment',
            'icon' => 'fas fa-credit-card',
            'route' => 'admin.settings.payment',
        ],
        'admin.settings.shipping' => [
            'label' => 'Shipping',
            'icon' => 'fas fa-truck',
            'route' => 'admin.settings.shipping',
        ],
        'admin.settings.notifications' => [
            'label' => 'Notifications',
            'icon' => 'fas fa-bell',
            'route' => 'admin.settings.notifications',
        ],
        'admin.settings.security' => [
            'label' => 'Security',
            'icon' => 'fas fa-shield-alt',
            'route' => 'admin.settings.security',
        ],
        'admin.backups.index' => [
            'label' => 'Backups',
            'icon' => 'fas fa-database',
            'route' => 'admin.backups.index',
        ],
    ];
@endphp

<div class="card card-primary card-outline mb-4">
    <div class="card-body p-0">
        <ul class="nav nav-tabs" role="tablist">

            @foreach ($tabs as $routeName => $tab)
                <li class="nav-item">
                    <a
                        href="{{ route($tab['route']) }}"
                        class="nav-link {{ request()->routeIs($routeName) ? 'active' : '' }}"
                    >
                        <i class="{{ $tab['icon'] }} mr-1"></i>
                        {{ $tab['label'] }}
                    </a>
                </li>
            @endforeach

        </ul>
    </div>
</div>