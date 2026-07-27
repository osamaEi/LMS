@props(['head' => []])
<div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="text-align:right;color:#64748b;">
                @foreach($head as $col)
                    <th style="padding:10px 14px;font-weight:700;border-bottom:1px solid #f1f5f9;white-space:nowrap;">{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody style="color:#1e293b;">
            {{ $slot }}
        </tbody>
    </table>
</div>
<style>
    [style*="border-collapse"] tbody td { padding:10px 14px; border-bottom:1px solid #f6f7f9; vertical-align:middle; }
</style>
