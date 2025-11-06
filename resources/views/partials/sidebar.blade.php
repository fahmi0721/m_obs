<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="../index.html" class="brand-link">
            <img src="{{ getLogoAplikasi() }}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
            <span class="brand-text">{{ getNamaAplikasi() }}</span>
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
        <!--begin::Sidebar Menu-->
        <ul
            class="nav sidebar-menu flex-column"
            data-lte-toggle="treeview"
            role="navigation"
            aria-label="Main navigation"
            data-accordion="false"
            id="navigation"
        >
          

            <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                <i class="nav-icon bi bi-speedometer"></i>
                <p>Dashboard</p>
            </a>
            </li>
            @if(auth()->user()->level != 'admin')
            <li class="nav-item">
            <a href="{{ route('profil') }}" class="nav-link {{ Route::is('profil') ? 'active' : '' }}">
                <i class="nav-icon fa fa-solid fa-user"></i>
                <p>My Profil</p>
            </a>
            </li>

            @endif

            @if(auth()->user()->level == 'admin')
            <li class="nav-header">GENERAL</li>
            <li class="nav-item">
            <a href="{{ route('setting') }}" class="nav-link {{ Route::is('setting') ? 'active' : '' }}">
                <i class="nav-icon fa fa-solid fa-gear"></i>
                <p>Pengaturan Umum</p>
            </a>
            </li>
            
            <li class="nav-item {{ setActive(['entitas', 'entitas.create', 'entitas.edit','project','project.create','regional', 'regional.create','unit', 'unit.create','job', 'job.create'], 'menu-open')  }}">
            <a href="#" class="nav-link {{ setActive(['entitas', 'entitas.create', 'entitas.edit','project','project.create','regional', 'regional.create','unit', 'unit.create','job', 'job.create'], 'active')  }}">
                <i class="nav-icon bi bi-clipboard-fill"></i>
                <p>
                Master Data
                <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="{{ route('entitas') }}" class="nav-link {{  setActive(['entitas', 'entitas.create', 'entitas.edit'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>Entitas</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="{{ route('project') }}" class="nav-link {{  setActive(['project', 'project.create'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>Project</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="{{ route('regional') }}" class="nav-link {{  setActive(['regional', 'regional.create'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>Regional</p>
                </a>
                </li>

                <li class="nav-item">
                <a href="{{ route('unit') }}" class="nav-link {{  setActive(['unit', 'unit.create'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>Unit</p>
                </a>
                </li>

                <li class="nav-item">
                <a href="{{ route('job') }}" class="nav-link {{  setActive(['job', 'job.create'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>Jabatan</p>
                </a>
                </li>
            </ul>
            </li>
            <li class="nav-item">
            <a href="{{ route('employee') }}" class="nav-link {{  setActive(['employee', 'employee.create'], 'active')  }}">
                <i class="nav-icon fa fa-users"></i>
                <p>Employee</p>
            </a>
            </li>

            <li class="nav-item">
            <a href="{{ route('formation') }}" class="nav-link {{  setActive(['formation', 'formation.create'], 'active')  }}">
                <i class="nav-icon fa fa-tag"></i>
                <p>Formation</p>
            </a>
            </li>
            <li class="nav-item {{ setActive(['sop', 'sop.create', 'sop.edit'], 'menu-open')  }}">
            <a href="#" class="nav-link {{ setActive(['sop', 'sop.create', 'sop.edit'], 'active')  }}">
                <i class="nav-icon fa fa-file-pdf"></i>
                <p>
                SOP
                <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="{{ route('sop') }}" class="nav-link {{  setActive(['sop', 'sop.create', 'sop.edit','sop_jabatan', 'sop_jabatan.create', 'sop_jabatan.edit'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>SOP Diatas Kapal</p>
                </a>
                </li>

                <li class="nav-item">
                <a href="{{ route('sop_jabatan') }}" class="nav-link {{  setActive(['sop_jabatan', 'sop_jabatan.create', 'sop_jabatan.edit'], 'active')  }}">
                    <i class="nav-icon fa fa-chevron-right fa-reguler"></i>
                    <p>SOP Jabatan</p>
                </a>
                </li>
                
            </ul>
            </li>

            <li class="nav-item">
            <a href="{{ route('video') }}" class="nav-link {{  setActive(['video', 'video.create'], 'active')  }}">
                <i class="nav-icon fa fa-video"></i>
                <p>Video</p>
            </a>
            </li>

            <li class="nav-item">
            <a href="{{ route('users') }}" class="nav-link {{  setActive(['users'], 'active')  }}">
                <i class="nav-icon fa fa-user"></i>
                <p>Users</p>
            </a>
            </li>

            
            @endif

            
        </ul>
        <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
    </aside>