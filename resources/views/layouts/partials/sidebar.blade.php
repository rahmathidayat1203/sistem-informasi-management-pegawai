<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        {{-- SVG Logo can be placed here or as a component --}}
      </span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2">SIMPEG</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Kepegawaian</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
        <a href="{{ route('admin.pegawai.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Pegawai">Manajemen Pegawai</div>
        </a>
    </li>

    <!-- Master Data -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Data Master</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.jabatan.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-briefcase"></i>
            <div data-i18n="Jabatan">Jabatan</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.golongan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.golongan.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-star"></i>
            <div data-i18n="Golongan">Golongan</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.unit_kerja.*') ? 'active' : '' }}">
        <a href="{{ route('admin.unit_kerja.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-building"></i>
            <div data-i18n="Unit Kerja">Unit Kerja</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.jenis_cuti.*') ? 'active' : '' }}">
        <a href="{{ route('admin.jenis_cuti.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar-check"></i>
            <div data-i18n="Jenis Cuti">Jenis Cuti</div>
        </a>
    </li>

    <!-- Riwayat -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Riwayat</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.pendidikan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.pendidikan.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-school"></i>
            <div data-i18n="Pendidikan">Pendidikan</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.keluarga.*') ? 'active' : '' }}">
        <a href="{{ route('admin.keluarga.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div data-i18n="Keluarga">Keluarga</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.riwayat_pangkat.*') ? 'active' : '' }}">
        <a href="{{ route('admin.riwayat_pangkat.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-trophy"></i>
            <div data-i18n="Riwayat Pangkat">Riwayat Pangkat</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.riwayat_jabatan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.riwayat_jabatan.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-award"></i>
            <div data-i18n="Riwayat Jabatan">Riwayat Jabatan</div>
        </a>
    </li>

    <!-- Cuti -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Cuti</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.cuti.*') ? 'active' : '' }}">
        <a href="{{ route('admin.cuti.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar-event"></i>
            <div data-i18n="Cuti">Pengajuan Cuti</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.sisa_cuti.*') ? 'active' : '' }}">
        <a href="{{ route('admin.sisa_cuti.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar-x"></i> <!-- Icon untuk sisa cuti, bisa diganti sesuai preferensi -->
            <div data-i18n="Sisa Cuti">Sisa Cuti</div>
        </a>
    </li>

    <!-- Perjalanan Dinas -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Perjalanan Dinas</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.perjalanan_dinas.*') ? 'active' : '' }}">
        <a href="{{ route('admin.perjalanan_dinas.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-trip"></i>
            <div data-i18n="Perjalanan Dinas">Perjalanan Dinas</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.laporan_pd.*') ? 'active' : '' }}">
        <a href="{{ route('admin.laporan_pd.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div data-i18n="Laporan PD">Laporan Perjalanan Dinas</div>
        </a>
    </li>

    <!-- Pengaturan -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Pengaturan</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-shield-alt-2"></i>
            <div data-i18n="Manajemen Akses">Manajemen Akses</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.roles.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-lock"></i>
                    <div data-i18n="Roles">Roles</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <a href="{{ route('admin.permissions.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-lock-alt"></i>
                    <div data-i18n="Permissions">Permissions</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div data-i18n="Users">Users</div>
                </a>
            </li>
        </ul>
    </li>


  </ul>
</aside>
<!-- / Menu -->