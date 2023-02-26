<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <table>
                <tr>
                    <td>
                        <div class="image">
                            <img src="images/logo.jpg" class="img-circle elevation-5" alt="User Image">
                        </div>
                    </td>
                    <td>
                        <div class="info">
                            <a href="#" class="d-block">Devie`s Print<br>
                                <small>Copy Center & Digital Printing</small> </a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @guest
                @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @endif

                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('produk-list')
                        <li class="nav-item">
                            <a href="{{ route('masterdata') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Produk</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                    <ul class="nav nav-treeview">
                        @can('unit-list')
                        <li class="nav-item">
                            <a href="{{ route('masteruom') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Satuan</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('kasir-list')
                        <li class="nav-item">
                            <a href="{{ route('datakasir') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kasir</p>
                            </a>
                        </li>
                        @endcan
                        @can('lunas-list')
                        <li class="nav-item">
                            <a href="{{ route('translunas') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pelunasan</p>
                            </a>
                        </li>
                        @endcan
                        @can('jual-list')
                        <li class="nav-item">
                            <a href="{{ route('penjualan2') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Penjualan</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Report
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('listprod-list')
                        <li class="nav-item">
                            <a href="{{ route('rptproduk') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List Produk</p>
                            </a>
                        </li>
                        @endcan
                        @can('listjual-list')
                        <li class="nav-item">
                            <a href="{{ route('penjualan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List Penjualan</p>
                            </a>
                        </li>
                        @endcan
                        @can('detjual-list')
                        <li class="nav-item">
                            <a href="{{ route('dtlpenjualan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Detail Penjualan</p>
                            </a>
                        </li>
                        @endcan
                        @can('uang-list')
                        <li class="nav-item">
                            <a href="{{ route('datakeu') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Keuangan</p>
                            </a>
                        </li>
                        @endcan
                        @can('kredit-list')
                        <li class="nav-item">
                            <a href="{{ route('kredit') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kredit</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Tools
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('menu-list')
                        <li class="nav-item">
                            <a href="{{ route('indexmenu') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Menu</p>
                            </a>
                        </li>
                        @endcan
                        @can('user-list')
                        <li class="nav-item">
                            <a href="{{ route('indexuser') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>
                        @endcan
                        @can('role-list')
                        <li class="nav-item">
                            <a href="{{ route('indexrole') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Group Access</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endguest
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>