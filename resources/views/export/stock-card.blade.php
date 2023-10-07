<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>QTY</th>
            <th>QTY Sisa</th>
            <th>Satuan</th>
            <th>Catatan</th>
            <th>Tgl. Dibuat</th>
            <th>Tgl. Terakhir Diubah</th>
        </tr>
    </thead>

    <tbody>
        @php $i = 0; @endphp
        @foreach ($data as $stockCard)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $stockCard->stock->item->name ?? '' }}</td>
            <td>
                @if ($stockCard->type == 'IN')
                    +
                @elseif ($stockCard->type == 'OUT')
                    -
                @endif
                {{ $stockCard->qty ?? 0 }}
            </td>
            <td>{{ $stockCard->qty_remain ?? 0 }}</td>
            <td>{{ $stockCard->stock->item->unit->short ?? '' }}</td>
            <td>{{ $stockCard->note ?? '' }}</td>
            <td>{{ \Carbon\Carbon::parse($stockCard->created_at)->format('Y-m-d H:i:s'); }}</td>
            <td>{{ \Carbon\Carbon::parse($stockCard->updated_at)->format('Y-m-d H:i:s'); }}</td>
        </tr>
        @php $i++; @endphp
        @endforeach
    </tbody>
</table>