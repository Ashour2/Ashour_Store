<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير التصنيفات</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; color:#222; padding:25px; } h1 { margin-bottom:5px; }
        .meta { color:#666; margin-bottom:20px; } table { width:100%; border-collapse:collapse; font-size:12px; }
        th, td { border:1px solid #bbb; padding:8px; text-align:right; } th { background:#1f4f78; color:#fff; }
        @media print { .no-print { display:none; } body { padding:0; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">حفظ كـ PDF / طباعة</button>
    <h1>تقرير التصنيفات</h1><div class="meta">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</div>
    <table><thead><tr><th>#</th><th>الاسم العربي</th><th>English name</th><th>عدد الأدوية</th></tr></thead>
    <tbody>@forelse($categories as $category)<tr><td>{{ $category->id }}</td><td>{{ $category->name_ar }}</td><td dir="ltr">{{ $category->name_en }}</td><td>{{ $category->products_count }}</td></tr>@empty<tr><td colspan="4">لا توجد بيانات</td></tr>@endforelse</tbody></table>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
