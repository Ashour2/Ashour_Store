<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير الأدوية</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; color:#222; padding:25px; } h1 { margin-bottom:5px; }
        .meta { color:#666; margin-bottom:20px; } table { width:100%; border-collapse:collapse; font-size:11px; }
        th, td { border:1px solid #bbb; padding:7px; text-align:right; } th { background:#1f4f78; color:#fff; }
        @media print { .no-print { display:none; } body { padding:0; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">حفظ كـ PDF / طباعة</button>
    <h1>تقرير الأدوية</h1><div class="meta">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</div>
    <table><thead><tr><th>#</th><th>الاسم العربي</th><th>English name</th><th>السعر</th><th>الكمية</th><th>التصنيف</th><th>الحالة</th></tr></thead>
    <tbody>@forelse($products as $product)@php $ownStock = auth()->user()->isPharmacist() ? $product->pharmacies->firstWhere('id', auth()->user()->pharmacy_id)?->pivot : null; @endphp<tr><td>{{ $product->id }}</td><td>{{ $product->name_ar }}</td><td dir="ltr">{{ $product->name_en }}</td><td>{{ $ownStock?->price ?? $product->price }}</td><td>{{ $ownStock?->quantity ?? $product->quantity }}</td><td>{{ $product->category?->trans_name }}</td><td>{{ $ownStock ? ($ownStock->is_available && $ownStock->quantity > 0 ? 'متوفر' : 'غير متوفر') : ($product->is_active ? 'نشط' : 'غير نشط') }}</td></tr>@empty<tr><td colspan="7">لا توجد بيانات</td></tr>@endforelse</tbody></table>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
