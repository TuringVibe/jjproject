<nav class="header navbar">
    <a href="{{route('project-dashboard.dashboard')}}">JJ PROJECT</a>
    @auth
    <form method="POST" action="{{route('logout')}}">
        @csrf
        <button class="logout"><i class="fas fa-power-off"></i></button>
    </form>
    @endauth
</nav>
