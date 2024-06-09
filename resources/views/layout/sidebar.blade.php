<div class="sidebar" data-color='dak-purple'>
    <div class="logo">
        <a href="#" class="simple-text logo-mini">
            <i class="bi bi-cash"></i>
        </a>
        <a href="#" class="simple-text logo-normal">
            Exchange
        </a>
    </div>
    @auth

    @php
    $items = Session::get('actions');
    @endphp
    <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
            @unless(array_key_exists('dashboard', $items) && in_array("tout",$items["dashboard"]))
            <li class=" ">
                <a href="{{route('admin.dashboard')}}">
                    <i class="bi bi-bar-chart"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            @endunless
            @if(auth()->user()->role == 'admin')
            <li class="">
                <a href="{{route('admin.pending-actions.index')}}">
                    <i class="bi bi-pencil"></i>
                    <p>Actions Comptable</p>
                </a>
            </li>
            @endif
            @unless(array_key_exists('devise', $items) && in_array("tout",$items["devise"]))
            <li>
                <a href="{{ route('devise.index') }}">
                    <i class="bi bi-currency-exchange"></i>
                    <p>Manage Devise</p>
                </a>
            </li>
            @endunless
            @unless(array_key_exists('client', $items) && in_array("tout",$items["client"]))
            <li class="">
                <a href="{{ route('clients.index') }}">
                    <i class="bi bi-people"></i>
                    <p>Clients</p>
                </a>
            </li>
            @endunless
            @unless(array_key_exists('stock', $items) && in_array("tout",$items["stock"]))
            <li>
                <a href="{{ route('stock.index') }}">
                    <i class="bi bi-box"></i>
                    <p>Stock</p>
                </a>
            </li>
            @endunless
            @if(auth()->user()->role == 'admin')
            <li>
                <a href="{{ route('comptables.index') }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <p>Comptables</p>
                </a>
            </li>
            @endif
            
            @unless(array_key_exists('profile', $items) && in_array("tout",$items["profile"]))
            <li>
                <a href="{{ route('profile.index') }}">
                    <i class="bi bi-gear"></i>
                    <p>Profile</p>
                </a>
            </li>
            @endunless
            @unless(array_key_exists('historique', $items) && in_array("tout",$items["historique"]))
            <li class="active-pro">
                <a href="{{ route('historique.index') }}">
                    <i class="bi bi-archive"></i>
                    <p>Historique Opérations</p>
                </a>
            </li>
            @endunless
        </ul>
    </div>
    @endauth
</div>