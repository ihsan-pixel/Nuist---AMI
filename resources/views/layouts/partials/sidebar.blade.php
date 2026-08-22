@php
    use App\Http\Controllers\Ami\AppSettingController;

    $role = auth()->user()?->role;
    $appLogoUrl = AppSettingController::logoUrl();
    $appName = \App\Models\AppSetting::query()->where('key', 'app_name')->value('value') ?? config('app.name');
    $appTagline = \App\Models\AppSetting::query()->where('key', 'app_tagline')->value('value') ?? 'Audit Mutu Internal';
    $menus = [
        'super_admin' => ['Dashboard', 'Periode AMI', 'Instrumen', 'Satuan Pendidikan', 'Auditor', 'Penugasan Auditor', 'Penilaian', 'Temuan', 'Tindak Lanjut', 'Laporan', 'Pengguna', 'Pengaturan'],
        'pengurus' => ['Dashboard', 'Monitoring', 'Temuan', 'Laporan AMI'],
        'auditor' => ['Dashboard', 'Audit AMI'],
        'sekolah' => ['Dashboard', 'Instrumen AMI', 'Tindak Lanjut'],
    ];
    $items = $menus[$role] ?? [];
@endphp
<aside class="fixed inset-y-0 left-0 z-30 hidden w-72 border-r border-slate-200 bg-white lg:block">
    <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-6">
        @if ($appLogoUrl)
            <img src="{{ $appLogoUrl }}" alt="Logo aplikasi" class="h-10 w-10 object-contain">
        @else
            <div class="flex h-10 w-10 items-center justify-center bg-[#00553F] text-xs font-semibold text-white">
                AMI
            </div>
        @endif
        <div class="min-w-0">
            <div class="truncate text-sm font-semibold tracking-wide text-[#00553F]">{{ $appName }}</div>
            <div class="truncate text-xs text-slate-500">{{ $appTagline }}</div>
        </div>
    </div>
    <nav class="px-3 py-4 text-sm">
        @foreach ($items as $item)
            @php
                $links = [
                    'Dashboard' => route('dashboard'),
                    'Periode AMI' => route('admin.ami-periods.index'),
                    'Instrumen' => route('admin.instruments.index'),
                    'Auditor' => route('admin.auditors.index'),
                    'Penugasan Auditor' => route('admin.auditor-assignments.index'),
                    'Penilaian' => route('admin.assessments.index'),
                    'Temuan' => route('admin.findings.index'),
                    'Tindak Lanjut' => route('admin.follow-ups.index'),
                    'Laporan' => route('admin.reports.index'),
                    'Pengguna' => route('admin.users.index'),
                    'Pengaturan' => route('admin.settings.index'),
                    'Penugasan AMI' => route('admin.assignments.index'),
                    'Satuan Pendidikan' => route('admin.schools.index'),
                    'Instrumen AMI' => route('school.ami.index'),
                    'Tindak Lanjut' => route('school.ami.follow-ups.index'),
                    'Audit AMI' => route('auditor.ami.index'),
                    'Monitoring' => route('pengurus.ami.index'),
                    'Temuan' => route('pengurus.findings.index'),
                    'Laporan AMI' => route('pengurus.ami.reports.index'),
                ];
                $href = $links[$item] ?? null;
            @endphp
            @if ($href)
                <a href="{{ $href }}" class="mb-1 block rounded-xl px-4 py-3 {{ request()->fullUrlIs($href) || request()->routeIs(match($item) {
                    'Dashboard' => 'dashboard',
                    'Periode AMI' => 'admin.ami-periods.*',
                    'Instrumen' => 'admin.instruments.*',
                    'Auditor' => 'admin.auditors.*',
                    'Penugasan Auditor' => 'admin.auditor-assignments.*',
                    'Penilaian' => 'admin.assessments.*',
                    'Temuan' => 'admin.findings.*',
                    'Tindak Lanjut' => 'admin.follow-ups.*',
                    'Laporan' => 'admin.reports.*',
                    'Pengguna' => 'admin.users.*',
                    'Penugasan AMI' => 'admin.assignments.*',
                    'Satuan Pendidikan' => 'admin.schools.*',
                    'Pengaturan' => 'admin.settings.*',
                    'Instrumen AMI' => 'school.ami.*',
                    'Tindak Lanjut' => 'school.ami.follow-ups.*',
                    'Audit AMI' => 'auditor.ami.*',
                    'Monitoring' => 'pengurus.ami.*',
                    'Temuan' => 'pengurus.findings.*',
                    'Laporan AMI' => 'pengurus.ami.reports.*',
                    default => '',
                }) ? 'bg-slate-100 text-[#00553F] font-medium' : 'text-slate-700 hover:bg-slate-50' }}">{{ $item }}</a>
            @else
                <span class="mb-1 block rounded-xl px-4 py-3 text-slate-400">{{ $item }}</span>
            @endif
        @endforeach
    </nav>
</aside>
