<nav class="sidebar">
    @if(Auth::user()->role == "admin")
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "events.list" ? "active" : ""}}" href="{{route('events.list')}}"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
    </ul>
    @endif
    <h5 class="sidebar-heading">PROJECTS</h5>
    <ul class="nav flex-column">
        @if(Auth::user()->role == "admin")
        <li class="nav-item"><a class="nav-link {{$active == "project-dashboard" ? "active" : ""}}" href="{{route('project-dashboard.dashboard')}}"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
        @endif
        <li class="nav-item"><a class="nav-link {{$active == "projects.list" ? "active" : ""}}" href="{{route('projects.list')}}"><i class="fas fa-th-list"></i> List</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "tasks.list" ? "active" : ""}}" href="{{route('tasks.list')}}"><i class="fas fa-tasks"></i> Tasks</a></li>
        @if(Auth::user()->role == "admin")
        <li class="nav-item"><a class="nav-link {{$active == "project-labels.list" ? "active" : ""}}" href="{{route('project-labels.list')}}"><i class="fas fa-tags"></i> Labels</a></li>
        @endif
    </ul>
    @if(Auth::user()->role == "admin")
    <h5 class="sidebar-heading">FINANCE</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "finance-dashboard" ? "active" : ""}}" href="{{route('finance-dashboard.dashboard')}}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "finance-mutations.list" ? "active" : ""}}" href="{{route('finance-mutations.list')}}"><i class="fas fa-balance-scale"></i> Mutations</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "finance-labels.list" ? "active" : ""}}" href="{{route('finance-labels.list')}}"><i class="fas fa-tags"></i> Labels</a></li>
    </ul>
    <h5 class="sidebar-heading">USER MANAGEMENT</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "users.list" ? "active" : ""}}" href="{{route('users.list')}}"><i class="fas fa-th-list"></i> List</a></li>
    </ul>
    @endif
</nav>
