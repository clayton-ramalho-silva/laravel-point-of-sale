@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-6 h-6"/>
                    </button>
                </div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-3">Notificações</h4>
                    <p class="mb-0">Acompanhe os avisos de estoque e atividades do sistema.</p>
                </div>
                <div>
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <x-heroicon-o-check class="w-5 h-5 mr-1" /> Marcar todas como lidas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>Status</th>
                            <th>Mensagem</th>
                            <th>Data</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($notifications as $notif)
                        <tr class="{{ $notif->is_read ? 'text-muted' : 'font-weight-bold' }}">
                            <td>
                                @if($notif->is_read)
                                    <span class="badge badge-secondary">Lida</span>
                                @else
                                    <span class="badge badge-danger">Nova</span>
                                @endif
                            </td>
                            <td>{{ $notif->message }}</td>
                            <td>{{ $notif->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if(!$notif->is_read)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" title="Marcar como lida">
                                        <x-heroicon-o-check class="w-4 h-4" />
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                <span class="text-muted">Nenhuma notificação encontrada.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
