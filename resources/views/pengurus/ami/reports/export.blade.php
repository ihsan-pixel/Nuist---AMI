<!doctype html>
<html><head><meta charset="utf-8"></head><body>
<table><tr><th>Periode</th><th>Tanggal Generate</th><th>Total Sekolah</th><th>Belum Submit</th><th>Audit Selesai</th><th>Follow-up Pending</th></tr>
<tr><td>{{ $period->name }}</td><td>{{ now()->format('Y-m-d H:i') }}</td><td>{{ $summary['total_schools'] }}</td><td>{{ $summary['not_submitted'] }}</td><td>{{ $summary['audit_completed'] }}</td><td>{{ $summary['follow_up_pending'] }}</td></tr></table>
<table><tr><th>SCOD</th><th>Nama Sekolah</th></tr>@foreach($schools as $assignment)<tr><td>{{ $assignment->school->scod }}</td><td>{{ $assignment->school->name }}</td></tr>@endforeach</table>
</body></html>
