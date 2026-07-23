@extends('admin.layouts.app')
@section('title', 'Báo cáo doanh thu')
@section('page_title', 'Báo Cáo Doanh Thu Năm ' . $currentYear)

@section('content')
<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card border-start border-4 border-success">
            <div class="vibe-stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <p class="vibe-stat-card-label">Doanh thu YTD</p>
                <h3 class="vibe-stat-card-value vibe-mono">{{ number_format($summary['total_revenue_ytd'], 0, '.', ',') }}₫</h3>
                @if($lastYearRevenue > 0)
                    @php $growth = (($summary['total_revenue_ytd'] - $lastYearRevenue) / $lastYearRevenue) * 100; @endphp
                    <small class="{{ $growth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $growth >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($growth), 1) }}% vs năm ngoái
                    </small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card border-start border-4 border-primary">
            <div class="vibe-stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <p class="vibe-stat-card-label">Tổng đơn hàng</p>
                <h3 class="vibe-stat-card-value">{{ $summary['total_orders_ytd'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card border-start border-4 border-info">
            <div class="vibe-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-receipt"></i></div>
            <div>
                <p class="vibe-stat-card-label">Giá trị trung bình/Đơn</p>
                <h3 class="vibe-stat-card-value vibe-mono">{{ number_format($summary['avg_order_value'], 0, '.', ',') }}₫</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="vibe-stat-card border-start border-4 border-warning">
            <div class="vibe-stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <p class="vibe-stat-card-label">Doanh thu tháng này</p>
                <h3 class="vibe-stat-card-value vibe-mono">{{ number_format($summary['this_month_revenue'], 0, '.', ',') }}₫</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="vibe-admin-card h-100">
            <h5 class="vibe-admin-card-title mb-4">Biểu đồ doanh thu năm {{ $currentYear }}</h5>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="vibe-admin-card h-100">
            <h5 class="vibe-admin-card-title mb-4">Tỉ lệ trạng thái đơn hàng</h5>
            <canvas id="statusChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="vibe-admin-card">
    <h5 class="vibe-admin-card-title mb-4">Top 10 Sản Phẩm Bán Chạy Nhất (Năm {{ $currentYear }})</h5>
    <div class="table-responsive">
        <table class="table vibe-admin-table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-center">Số lượng bán</th>
                    <th class="text-end">Doanh thu mang lại</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $index => $p)
                <tr>
                    <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $p->product_name }}</td>
                    <td class="text-center"><span class="badge bg-dark">{{ $p->total_sold }}</span></td>
                    <td class="text-end vibe-mono fw-bold text-success">{{ number_format($p->revenue, 0, '.', ',') }}₫</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (VND)',
                data: [{{ implode(',', array_column($revenueData, 'revenue')) }}],
                backgroundColor: '#111',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map(function($key) { return \App\Models\Order::$statusLabels[$key] ?? $key; }, array_keys($ordersByStatus->toArray()))) !!},
            datasets: [{
                data: {{ json_encode(array_values($ordersByStatus->toArray())) }},
                backgroundColor: ['#ffc107', '#0dcaf0', '#0d6efd', '#198754', '#dc3545', '#6c757d', '#212529']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush
@endsection
