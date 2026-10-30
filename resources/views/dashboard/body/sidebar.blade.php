
<div class="iq-sidebar sidebar-default ">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">POSDash</h5>
        </a>
        <div class="iq-menu-bt-sidebar ml-0">
            <x-heroicon-o-bars-3 class="wrapper-menu w-8 h-8" />
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="svg-icon">
                        <x-heroicon-o-home class="w-6 h-6" />
                        <span class="ml-4">Dashboards</span>
                    </a>
                </li>

                @if (auth()->user()->can('pos.menu'))
                    <li class="{{ Request::is('pos*') ? 'active' : '' }}">
                        <a href="{{ route('pos.index') }}" class="svg-icon">
                            <x-heroicon-o-shopping-cart class="w-6 h-6" />
                            <span class="ml-3">POS</span>
                            </a>
                            </li>
                @endif

                <hr>

                @if (auth()->user()->can('orders.menu'))
                    <li>
                        <a href="#orders" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <x-heroicon-o-shopping-bag class="w-6 h-6" />
                            <span class="ml-3">Pedidos</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="orders" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            <li class="{{ Request::is('orders/pending*') ? 'active' : '' }}">
                                <a href="{{ route('order.pendingOrders') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pedidos Pendentes</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('orders/complete*') ? 'active' : '' }}">
                                        <a href="{{ route('order.completeOrders') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pedidos Concluídos</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pending/due*') ? 'active' : '' }}">
                                        <a href="{{ route('order.pendingDue') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pedidos Vencidos</span>
                                    </a>
                                    </li>

                                    </ul>
                                    </li>
                @endif

                @if (auth()->user()->can('product.menu'))
                    <li>
                        <a href="#products" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <x-heroicon-o-archive-box class="w-6 h-6" />
                            <span class="ml-3">Produtos</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="products" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is(['products']) ? 'active' : '' }}">
                                    <a href="{{ route('products.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Produtos</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['products/create']) ? 'active' : '' }}">
                                            <a href="{{ route('products.create') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Adicionar Produto</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['categories*']) ? 'active' : '' }}">
                                            <a href="{{ route('categories.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Categorias</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif

                <li class="{{ Request::is('notifications*') ? 'active' : '' }}">
                    <a href="{{ route('notifications.index') }}" class="svg-icon">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span class="ml-3">Notificações</span>

                        @php
                            $unreadCount = \App\Models\AppNotification::where('is_read', false)->count();
                        @endphp

                        @if($unreadCount > 0)
                            <span class="badge badge-danger badge-pill float-right mt-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>

                <hr>

                @if (auth()->user()->can('employee.menu'))
                    <li class="{{ Request::is('employees*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Empregados</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('customer.menu'))
                    <li class="{{ Request::is('customers*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Clientes</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('supplier.menu'))
                    <li class="{{ Request::is('suppliers*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Fornecedores</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('salary.menu'))
                    <li>
                        <a href="#advance-salary" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                        <span class="ml-3">Salário</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                        </a>
                        <ul id="advance-salary" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            <li class="{{ Request::is(['advance-salary', 'advance-salary/*/edit']) ? 'active' : '' }}">
                                <a href="{{ route('advance-salary.index') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Adiantamento integral de salário</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('advance-salary/create*') ? 'active' : '' }}">
                                        <a href="{{ route('advance-salary.create') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Adicionar Adiantamento de Salário</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pay-salary') ? 'active' : '' }}">
                                        <a href="{{ route('pay-salary.index') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pagar Salário</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pay-salary/history*') ? 'active' : '' }}">
                                        <a href="{{ route('pay-salary.payHistory') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Histórico de Pagamento de Salário</span>
                                    </a>
                                    </li>
                                    </ul>
                                    </li>
                @endif

                @if (auth()->user()->can('attendance.menu'))
                    <li>
                        <a href="#attendance" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                            <span class="ml-3">Presença</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="attendance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                                <li class="{{ Request::is(['attendance']) ? 'active' : '' }}">
                                    <a href="{{ route('attendance.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Todas as Presenças</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is('attendance/create') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.create') }}">
                                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Criar Presença</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif

                <hr>


                @if (auth()->user()->can('roles.menu'))
                    <li>
                        <a href="#permission" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <x-heroicon-o-key class="w-6 h-6" />
                            <span class="ml-3">Função e Permissão</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="permission" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is(['permission', 'permission/create', 'permission/edit/*']) ? 'active' : '' }}">
                                    <a href="{{ route('permission.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Permissões</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['role', 'role/create', 'role/edit/*']) ? 'active' : '' }}">
                                            <a href="{{ route('role.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Funções</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['role/permission*']) ? 'active' : '' }}">
                                            <a href="{{ route('rolePermission.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Função em Permissões</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif

                @if (auth()->user()->can('user.menu'))
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="svg-icon">
                            <x-heroicon-o-users class="w-6 h-6" />
                            <span class="ml-3">Usuários</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('database.menu'))
                    <li class="{{ Request::is('database/backup*') ? 'active' : '' }}">
                        <a href="{{ route('backup.index') }}" class="svg-icon">
                            <x-heroicon-o-circle-stack class="w-6 h-6" />
                            <span class="ml-3">Backup Database</span>
                            </a>
                            </li>
                @endif

                <li class="{{ Request::is('help*') ? 'active' : '' }}">
                    <a href="{{ route('help.index') }}" class="svg-icon">
                        <x-heroicon-o-question-mark-circle class="w-6 h-6" />
                        <span class="ml-3">Ajuda</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
